<?php

namespace Andrmoel\AstronomyBundle\Parsers;

use Andrmoel\AstronomyBundle\Entities\TwoLineElements;

class TLEParser extends AbstractParser
{
    /** @var TwoLineElements */
    private $tle;

    public function __construct()
    {
        $this->tle = new TwoLineElements();
    }

    public function getParsedData()
    {
        $lines = explode("\n", $this->data);

        $pattern = '/^([12]) /i';
        foreach ($lines as $line) {
            if (preg_match($pattern, $line, $matches)) {
                switch ($matches[1]) {
                    case 1:
                        $this->parseLineOne($line);
                        break;
                    case 2:
                        $this->parseLineTwo($line);
                        break;
                }
            }
        }
        // TODO: Implement getParsedData() method.
    }

    private function parseLineOne(string $line): void
    {
        $tle = $this->tle;

        $satelliteNo = substr($line, 2, 4);
        $classification = substr($line, 7, 1);
        $internationalDesignator = trim(substr($line, 9, 8));
        $year = $this->getYearFromEpochYear(substr($line, 18, 2));
        $epochDay = floatval(substr($line, 20, 12));
        $meanMotion = substr($line, 33, 10);
    }

    private function parseLineTwo(string $line): void
    {
        $tle = $this->tle;

        $inclination = floatval(substr($line, 8, 8));
        $rightAscension = floatval(substr($line, 17, 8));
        $eccentricity = floatval('0.' . trim(substr($line, 26, 7)));
        $argumentOfPerigee = floatval(substr($line, 34, 8));
        $meanAnomaly = floatval(substr($line, 43, 8));
        $meanMotion = floatval(substr($line, 52, 11));
        $revolutionNoAtEpoch = (int)substr($line, 63, 5);

        var_dump($eccentricity);
    }

    private function getYearFromEpochYear(string $epochYear): int
    {
        if (floatval($epochYear) < 50) {
            return '20' . $epochYear;
        } else {
            return '19' . $epochYear;
        }
    }
}
