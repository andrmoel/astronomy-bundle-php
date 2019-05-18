<?php

require __DIR__ . '/../vendor/autoload.php';

use Andrmoel\AstronomyBundle\Utils\GeneralUtil;

echo "Read eclipse list from NASAs website\n";
$eclipseList = getEclipseList();

echo 'Total amount of eclipses found: ' . count($eclipseList) . "\n";

foreach ($eclipseList as $eclipse) {
    echo "Download Besselian Elemenets for $eclipse\n";
    $url = generateUrl($eclipse);
    download($url, $eclipse . '.txt');
}

echo "FINISHED\n";

function getEclipseList(): array
{
    $result = [];

    $yearStart = -1999;
    $yearMax = 3000;

    for ($year = $yearStart; $year < $yearMax; $year += 100) {
        echo "* Check year $year ...";
        $url = 'https://eclipse.gsfc.nasa.gov/SEcat5/SE'
            . GeneralUtil::year2string($year) . '-'
            . GeneralUtil::year2string($year + 99) . '.html';

        $eclipses = getEclipsesFromListUrl($url);
        echo 'found ' . count($eclipses) . " eclipses\n";

        $result = array_merge($eclipses, $result);
    }

    return $result;
}

function getEclipsesFromListUrl(string $url): array
{
    $content = file_get_contents($url);

    $pattern = '/SEdata\.php\?Ecl=(-?[0-9]+)/si';

    if (preg_match_all($pattern, $content, $matches)) {
        return $matches[1];
    }

    return [];
}

function generateUrl(string $date): string
{
    $url = 'https://eclipse.gsfc.nasa.gov/SEsearch/SEdata.php?Ecl=' . $date;

    return $url;
}

function download(string $url, string $fileName): void
{
    $file = __DIR__ . '/../src/Resources/besselianElements/' . $fileName;

    if (!file_exists($file)) {
        $content = file_get_contents($url);

        $pattern = '/<pre>(.*?)<\/pre>/si';
        if (preg_match($pattern, $content, $matches)) {
            $besselianElements = strip_tags($matches[1]);
            $besselianElements = html_entity_decode($besselianElements);

            file_put_contents($file, $besselianElements);
        }
    }
}
