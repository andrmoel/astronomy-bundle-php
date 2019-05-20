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
    $file = __DIR__ . '/../src/Resources/VSOP87/serialized/' . $fileName . '.php';

    $content = '<?php' . "\n";
    $content .= 'return [' . "\n";

    foreach ($data as $key => $value) {
        $content .= '  ' . $key . ' => [' . "\n";

        foreach ($value as $key2 => $value2) {
            $content .= '    ' . $key2 . ' => [' . "\n";

            foreach ($value2 as $key3 => $value3) {
                $content .= '      ' . $key3 . ' => [' . "\n";

                foreach ($value3 as $key4 => $value4) {
                    $content .= '        \'' . $key4 . '\' => ' . $value4 . ",\n";
                }

                $content .= '      ' . "],\n";
            }

            $content .= '    ' . "],\n";
        }

        $content .= '  ' . "],\n";
    }

    $content .= '];';

    file_put_contents($file, $content);
}
