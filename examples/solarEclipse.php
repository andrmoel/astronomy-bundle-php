<?php

include __DIR__ . '/../vendor/autoload.php';

use Andrmoel\AstronomyBundle\Eclipses\BesselianElements;
use Andrmoel\AstronomyBundle\Eclipses\SolarEclipse;
use Andrmoel\AstronomyBundle\Location;

// Madras, Oregon
$location = new Location(44.61040, -121.23848);

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
Location: {$location->getLatitude()}°, {$location->getLongitude()}°
Eclipse type: {$solarEclipse->getEclipseType()}
Duration complete: {$solarEclipse->getEclipseDuration()} seconds
Duration umbra: {$solarEclipse->getEclipseUmbraDuration()} seconds
Coverage: {$solarEclipse->getCoverage()}
Magnitude: {$max->getMagnitude()}
Moon-sun-ratio: {$max->getMoonSunRatio()}

C1: {$solarEclipse->getTimeOfInterest($c1)->getDateTime()->format('Y-m-d H:i:s')} UTC
C2: {$solarEclipse->getTimeOfInterest($c2)->getDateTime()->format('Y-m-d H:i:s')} UTC
Max: {$solarEclipse->getTimeOfInterest($max)->getDateTime()->format('Y-m-d H:i:s')} UTC
C3: {$solarEclipse->getTimeOfInterest($c3)->getDateTime()->format('Y-m-d H:i:s')} UTC
C4: {$solarEclipse->getTimeOfInterest($c4)->getDateTime()->format('Y-m-d H:i:s')} UTC

END;
