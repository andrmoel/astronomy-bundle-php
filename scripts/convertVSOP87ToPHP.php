<?php

require __DIR__ . '/../vendor/autoload.php';

use Andrmoel\AstronomyBundle\Parsers\ParserFactory;
use Andrmoel\AstronomyBundle\Parsers\VSOP87Parser;

$dir = __DIR__ . '/../src/Resources/VSOP87/';

$handle = opendir($dir);
while ($fileName = readdir($handle)) {
    if ($fileName != '.' && $fileName != '..' && !is_dir($dir . $fileName)) {
        $content = file_get_contents($dir . $fileName);
        $parser = ParserFactory::get(VSOP87Parser::class, $content);

        $data = $parser->getParsedData();

        dataToPHPFile($data, $fileName);
    }
}

function dataToPHPFile(array $data, string $fileName): void
{
    $fileName = generateClassName($fileName);
    $file = __DIR__ . '/../src/Calculations/VSOP87/' . $fileName . '.php';

    $content = '<?php' . "\n\n";

    $content .= 'namespace Andrmoel\\AstronomyBundle\\Calculations\\VSOP87' . ";\n\n";
    $content .= 'class ' . $fileName . " implements VSOP87Interface\n{";

    foreach ($data as $key => $value) {
        switch ($key) {
            default:
            case 1:
                $term = 'A';
                break;
            case 2:
                $term = 'B';
                break;
            case 3:
                $term = 'C';
                break;
        };

        foreach ($value as $key2 => $value2) {
            $content .= "\n" . '    public static function calculate' . $term . $key2 . "(\$t): float\n    {\n";

            $content .= '        return ';

            foreach ($value2 as $key3 => $value3) {
                $A = $value3['A'];
                $B = $value3['B'];
                $C = $value3['C'];

                if ($key3 > 0) {
                    $content .= "\n            +";
                }

                $content .= " $A * cos($B + $C * \$t)";
            }

            $content .= ";\n    }\n";
        }
    }

    $content .= "}\n";

    file_put_contents($file, $content);
}

function generateClassName(string $fileName): string
{
    $pattern = '/VSOP87([A-F])\.([a-z]{3})$/';

    if (preg_match($pattern, $fileName, $matches)) {
        switch ($matches[2]) {
            case 'ear':
                $part1 = 'Earth';
                break;
            case 'jup':
                $part1 = 'Jupiter';
                break;
            case 'mar':
                $part1 = 'Mars';
                break;
            case 'mer':
                $part1 = 'Mercury';
                break;
            case 'nep':
                $part1 = 'Neptune';
                break;
            case 'sat':
                $part1 = 'Saturn';
                break;
            case 'ura':
                $part1 = 'Uranus';
                break;
            case 'ven':
                $part1 = 'Venus';
                break;
        }
        switch ($matches[1]) {
            case 'C':
                $part2 = 'Rectangular';
                break;
            case 'D':
                $part2 = 'Spherical';
                break;
        }

        return $part1 . $part2 . 'VSOP87';
    }

    return '';
}
