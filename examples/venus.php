<?php

include __DIR__ . '/../vendor/autoload.php';

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Venus;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

date_default_timezone_set('UTC');

$location = Location::create(52.524, 13.411); // Berlin

$toi = TimeOfInterest::createFromString('2018-10-25 07:15:00');
$venus = Venus::create($toi);

// Heliocentric ecliptical rectangular coordinates
$helEclRecCoords = $venus->getHeliocentricEclipticalRectangularCoordinates();
$x = $helEclRecCoords->getX();
$y = $helEclRecCoords->getY();
$z = $helEclRecCoords->getZ();

// Heliocentric ecliptical spherical coordinates
$helEclSphCoords = $venus->getHeliocentricEclipticalSphericalCoordinates();
$helLat = $helEclSphCoords->getLatitude();
$helLat = AngleUtil::dec2angle($helLat);
$helLon = $helEclSphCoords->getLongitude();
$helLon = AngleUtil::dec2angle($helLon);

// Geocentric ecliptical spherical coordinates
$geoEclSphCoords = $venus->getGeocentricEclipticalSphericalCoordinates();

$eclLon = $geoEclSphCoords->getLongitude();
$eclLon = AngleUtil::dec2angle($eclLon);
$eclLat = $geoEclSphCoords->getLatitude();
$eclLat = AngleUtil::dec2angle($eclLat);
$radiusVector = $geoEclSphCoords->getRadiusVector();

// Geocentric equatorial spherical coordinates
$geoEqaCoords = $venus->getGeocentricEquatorialSphericalCoordinates();

$rightAscension = $geoEqaCoords->getRightAscension();
$rightAscension = AngleUtil::dec2time($rightAscension);
$declination = $geoEqaCoords->getDeclination();
$declination = AngleUtil::dec2angle($declination);

// Local horizontal coordinates
$localHorizontalCoords = $venus->getLocalHorizontalCoordinates($location);

$azimuth = $localHorizontalCoords->getAzimuth();
$altitude = $localHorizontalCoords->getAltitude();

// Rise, set and upper culmination
$rise = $venus->getRise($location);
$culmination = $venus->getUpperCulmination($location);
$set = $venus->getSet($location);

echo <<<END
+------------------------------------
| Venus
+------------------------------------
Date: {$toi} UTC

Heliocentric coordinates
X: {$x}
Y: {$y}
z: {$z}
Ecliptical latitude: {$helLat}
Ecliptical longitude: {$helLon}

Geocentric coordinates (apparent)
Ecliptical longitude: {$eclLon}
Ecliptical latitude: {$eclLat}
Right ascension: {$rightAscension}
Declination: {$declination}

Venus seen from observer's location
Location: {$location->getLatitude()}°, {$location->getLongitude()}°
Azimuth: {$azimuth}
Altitude: {$altitude}
Rise: {$rise->getDateTime()->format('Y-m-d H:i:s')} UTC
Culmination: {$culmination->getDateTime()->format('Y-m-d H:i:s')} UTC
Set: {$set->getDateTime()->format('Y-m-d H:i:s')} UTC

END;
