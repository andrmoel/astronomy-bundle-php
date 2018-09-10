<?php

include __DIR__ . '/../vendor/autoload.php';

use Andrmoel\AstronomyBundle\Eclipses\BesselianElements;
use Andrmoel\AstronomyBundle\Eclipses\SolarEclipse;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;

$besselianElements = new BesselianElements(include __DIR__ . '/testData/besselianElements.php');

// Madras, Oregon
$lat = 44.61040;
$lon = -121.23848;
$location = new Location($lat, $lon);

// Time of interest is the 21th of August 2017
$toi = new TimeOfInterest();
$toi->setTime(2017, 8, 21);

$solarEclipse = new SolarEclipse($besselianElements);
$solarEclipse->setLocation($location);

$c1 = $solarEclipse->getCircumstancesC1();
$c2 = $solarEclipse->getCircumstancesC2();
$max = $solarEclipse->getCircumstancesMax();
$c3 = $solarEclipse->getCircumstancesC3();
$c4 = $solarEclipse->getCircumstancesC4();

echo <<<END
Location: {$lat}°, {$lon}°
Eclipse type: {$solarEclipse->getEclipseType()}
Duration complete: {$solarEclipse->getEclipseDuration()} seconds
Duration umbra: {$solarEclipse->getEclipseUmbraDuration()} seconds
Coverage: {$solarEclipse->getCoverage()}

C1: {$solarEclipse->getTimeOfInterest($c1)->getTimeString()}
C2: {$solarEclipse->getTimeOfInterest($c2)->getTimeString()}
MAX: {$solarEclipse->getTimeOfInterest($max)->getTimeString()}
C3: {$solarEclipse->getTimeOfInterest($c3)->getTimeString()}
C4: {$solarEclipse->getTimeOfInterest($c4)->getTimeString()}

END;
