<?php

include __DIR__ . '/../vendor/autoload.php';

use Andrmoel\AstronomyBundle\Eclipses\BesselianElements;
use Andrmoel\AstronomyBundle\Eclipses\SolarEclipse;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;

// Madras, Oregon
$lat = 44.61040;
$lon = -121.23848;
$location = new Location($lat, $lon);

// Besselian elements for the given eclipse
// https://eclipse.gsfc.nasa.gov/SEbeselm/SEbeselm2001/SE2017Aug21Tbeselm.html
$besselianElements = new BesselianElements(include __DIR__ . '/testData/besselianElements.php');

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

C1: {$solarEclipse->getTimeOfInterest($c1)->getDateTime()->format('Y-m-d H:i:s')}
C2: {$solarEclipse->getTimeOfInterest($c2)->getDateTime()->format('Y-m-d H:i:s')}
MAX: {$solarEclipse->getTimeOfInterest($max)->getDateTime()->format('Y-m-d H:i:s')}
C3: {$solarEclipse->getTimeOfInterest($c3)->getDateTime()->format('Y-m-d H:i:s')}
C4: {$solarEclipse->getTimeOfInterest($c4)->getDateTime()->format('Y-m-d H:i:s')}

END;
