<?php

include __DIR__ . '/../vendor/autoload.php';

use Andrmoel\AstronomyBundle\AstronomicalObjects\Earth;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Venus;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

// Berlin
$lat = AngleUtil::angle2dec('52°31\'27.73"');
$lon = AngleUtil::angle2dec('13°24\'37.91"');
$location = new Location($lat, $lon);

// Time of interest
$dateTime = new DateTime('2018-05-13 20:00:00');
$toi = new TimeOfInterest($dateTime);

$earth = new Earth($toi);
$e = $earth->getObliquityOfEcliptic();

// Create venus
$venus = new Venus($toi);

// Get ecliptical coordinates
$eclipticalCoordinates = $venus
    ->getHeliocentricEclipticalCoordinates()
    ->getEclipticalCoordinates($toi);

$eclLatitude = $eclipticalCoordinates->getLatitude();
$eclLatitude = AngleUtil::dec2angle($eclLatitude);
$eclLongitude = $eclipticalCoordinates->getLongitude();
$eclLongitude = AngleUtil::dec2angle($eclLongitude);

// Get equatorial coordinates
$equatorialCorodinates = $eclipticalCoordinates
    ->getEquatorialCoordinates($e);

$rightAscension = $equatorialCorodinates->getRightAscension();
$rightAscension = AngleUtil::dec2time($rightAscension);
$declination = $equatorialCorodinates->getDeclination();
$declination = AngleUtil::dec2angle($declination);

// Get local horizontal coordinates
$localHorizontalCoordinates = $equatorialCorodinates->getLocalHorizontalCoordinates($location, $toi);
$azimuth = $localHorizontalCoordinates->getAzimuth() + 180; // TODO FALSCHER WERT. Laut Stellarium 294.45...
$azimuth = AngleUtil::dec2angle($azimuth);
$altitude = $localHorizontalCoordinates->getAltitude();
$altitude = AngleUtil::dec2angle($altitude);

echo <<<END
+------------------------------------
| Moon
+------------------------------------
Date: {$toi->getDateTime()->format('Y-m-d H:i:s')}
Ecliptical latitude: {$eclLatitude}
Ecliptical longitude: {$eclLongitude}
Right ascending: {$rightAscension}
Declination: {$declination}

Position: {$lat}°, {$lon}°
Azimuth: {$azimuth}
Altitude: {$altitude}

END;
