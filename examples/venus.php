<?php

include __DIR__ . '/../vendor/autoload.php';

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Venus;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

date_default_timezone_set('UTC');

// Berlin
$location = new Location(52.524, 13.411);

$toi = new TimeOfInterest(new DateTime('2019-05-23 00:00:00'));

$venus = new Venus($toi);

// Ecliptical spherical coordinates
$geoEclSphCoordinates = $venus->getGeocentricEclipticalSphericalCoordinates();

$eclLon = $geoEclSphCoordinates->getLongitude();
$eclLon = AngleUtil::dec2angle($eclLon);
$eclLat = $geoEclSphCoordinates->getLatitude();
$eclLat = AngleUtil::dec2angle($eclLat);
$radiusVector = $geoEclSphCoordinates->getRadiusVector();

// Equatorial coordinates
$geoEqaCoordinates = $venus->getGeocentricEquatorialSphericalCoordinates();

$rightAscension = $geoEqaCoordinates->getRightAscension();
$rightAscension = AngleUtil::dec2time($rightAscension);
$declination = $geoEqaCoordinates->getDeclination();
$declination = AngleUtil::dec2angle($declination);

// Local horizontal coordinates
$localHorizontalCoordinates = $venus->getLocalHorizontalCoordinates($location);

$azimuth = $localHorizontalCoordinates->getAzimuth();
$altitude = $localHorizontalCoordinates->getAltitude();

echo <<<END
+------------------------------------
| Venus
+------------------------------------
Date: {$toi->getDateTime()->format('Y-m-d H:i:s')} UTC

Ecliptical longitude: {$eclLon}
Ecliptical latitude: {$eclLat}
Right ascension: {$rightAscension}
Declination: {$declination}

The sun seen from observer's location
Location: {$location->getLatitude()}°, {$location->getLongitude()}°
Azimuth: {$azimuth} (apparent)
Altitude: {$altitude} (apparent)

END;
