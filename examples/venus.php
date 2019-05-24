<?php

include __DIR__ . '/../vendor/autoload.php';

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Venus;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

date_default_timezone_set('UTC');

$location = new Location(52.524, 13.411); // Berlin

$dateTime = new DateTime('2018-10-25 07:15:00');
$toi = new TimeOfInterest();

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

// Rise, set and upper culmination
$rise = $venus->getRise($location);
$culmination = $venus->getUpperCulmination($location);
$set = $venus->getSet($location);

echo <<<END
+------------------------------------
| Venus
+------------------------------------
Date: {$toi->getDateTime()->format('Y-m-d H:i:s')} UTC

Ecliptical longitude: {$eclLon}
Ecliptical latitude: {$eclLat}
Right ascension: {$rightAscension}
Declination: {$declination}

Venus seen from observer's location
Location: {$location->getLatitude()}°, {$location->getLongitude()}°
Azimuth: {$azimuth} (apparent)
Altitude: {$altitude} (apparent)
Rise: {$rise->getDateTime()->format('Y-m-d H:i:s')} UTC
Culmination: {$culmination->getDateTime()->format('Y-m-d H:i:s')} UTC
Set: {$set->getDateTime()->format('Y-m-d H:i:s')} UTC

END;
