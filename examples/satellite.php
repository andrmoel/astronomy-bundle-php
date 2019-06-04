<?php

include __DIR__ . '/../vendor/autoload.php';

use Andrmoel\AstronomyBundle\Events\SolarEclipse\SolarEclipse;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;

date_default_timezone_set('UTC');

$string = <<<END
ISS (ZARYA)
1 25544U 98067A   08264.51782528 -.00002182  00000-0 -11606-4 0  2927
2 25544  51.6416 247.4627 0006703 130.5360 325.0288 15.72125391563537
END;

$TLEParser = \Andrmoel\AstronomyBundle\Parsers\ParserFactory::get(\Andrmoel\AstronomyBundle\Parsers\TLEParser::class, $string);
$res = $TLEParser->getParsedData();

var_dump($res);die();