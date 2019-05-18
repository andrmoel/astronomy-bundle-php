<?php

include __DIR__ . '/../vendor/autoload.php';

use Andrmoel\AstronomyBundle\Events\SolarEclipse\SolarEclipse;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;

date_default_timezone_set('UTC');

// Madras, Oregon
$location = new Location(44.61040, -121.23848);

// Time of interest (Great American Eclipse)
$dateTime = new DateTime('2017-08-21');
$toi = new TimeOfInterest($dateTime);

// Create solar eclipse
$solarEclipse = SolarEclipse::create($toi, $location);

$c1 = $solarEclipse->getCircumstancesC1();
$c2 = $solarEclipse->getCircumstancesC2();
$max = $solarEclipse->getCircumstancesMax();
$c3 = $solarEclipse->getCircumstancesC3();
$c4 = $solarEclipse->getCircumstancesC4();

echo <<<END
+------------------------------------
| Solar eclipse
+------------------------------------
Location: {$location->getLatitude()}°, {$location->getLongitude()}° (Madras, OR - USA)

Eclipse type: {$solarEclipse->getEclipseType()}
Eclipse duration: {$solarEclipse->getEclipseDuration()} seconds
Totality duration: {$solarEclipse->getEclipseUmbraDuration()} seconds
Coverage: {$solarEclipse->getCoverage()}
Magnitude: {$solarEclipse->getMagnitude()}
Moon-sun-ratio: {$solarEclipse->getMoonSunRatio()}

Contact times
C1: {$solarEclipse->getTimeOfInterest($c1)->getDateTime()->format('Y-m-d H:i:s')} UTC
C2: {$solarEclipse->getTimeOfInterest($c2)->getDateTime()->format('Y-m-d H:i:s')} UTC
Max: {$solarEclipse->getTimeOfInterest($max)->getDateTime()->format('Y-m-d H:i:s')} UTC
C3: {$solarEclipse->getTimeOfInterest($c3)->getDateTime()->format('Y-m-d H:i:s')} UTC
C4: {$solarEclipse->getTimeOfInterest($c4)->getDateTime()->format('Y-m-d H:i:s')} UTC

END;
