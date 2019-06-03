<?php

include __DIR__ . '/../vendor/autoload.php';

use Andrmoel\AstronomyBundle\Events\SolarEclipse\SolarEclipse;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;

date_default_timezone_set('UTC');

$string = <<<END
ISS (ZARYA)
1 25544U 98067A   19154.33214331 -.00000033  00000-0  72788-5 0  9994
2 25544  51.6450  70.0584 0007587   8.8723  71.1071 15.51163107173101
END;

$TLEParser = \Andrmoel\AstronomyBundle\Parsers\ParserFactory::get(\Andrmoel\AstronomyBundle\Parsers\TLEParser::class, $string);
$res = $TLEParser->getParsedData();

var_dump($res);die();