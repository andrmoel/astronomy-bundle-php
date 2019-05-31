<?php

include __DIR__ . '/../vendor/autoload.php';

use Andrmoel\AstronomyBundle\AstronomicalObjects\Moon;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

date_default_timezone_set('UTC');

// Berlin
$location = new Location(52.52, 13.405);

// Create moon
$moon = new Moon();

// Ecliptical spherical coordinates
$geoEclSphCoordinates = $moon->getGeocentricEclipticalSphericalCoordinates();

$eclLon = $geoEclSphCoordinates->getLongitude();
$eclLon = AngleUtil::dec2angle($eclLon);
$eclLat = $geoEclSphCoordinates->getLatitude();
$eclLat = AngleUtil::dec2angle($eclLat);
$radiusVector = $geoEclSphCoordinates->getRadiusVector();

// Equatorial coordinates
$geoEqaCoordinates = $moon->getGeocentricEquatorialSphericalCoordinates();

$rightAscension = $geoEqaCoordinates->getRightAscension();
$rightAscension = AngleUtil::dec2time($rightAscension);
$declination = $geoEqaCoordinates->getDeclination();
$declination = AngleUtil::dec2angle($declination);

// Local horizontal coordinates
$localHorizontalCoordinates = $moon->getLocalHorizontalCoordinates($location);

$azimuth = $localHorizontalCoordinates->getAzimuth();
$altitude = $localHorizontalCoordinates->getAltitude();

$distance = $moon->getDistanceToEarth();
$isWaxingMoon = $moon->isWaxingMoon() ? 'yes' : 'no';
$illuminatedFraction = $moon->getIlluminatedFraction();
$illuminatedFraction = round($illuminatedFraction * 100, 1);
$positionAngleOfBrightLimb = $moon->getPositionAngleOfMoonsBrightLimb();

echo <<<END
+------------------------------------
| Moon
+------------------------------------
Date: {$moon->getTimeOfInterest()} UTC

Ecliptical longitude: {$eclLon}
Ecliptical latitude: {$eclLat}
Right ascension: {$rightAscension}
Declination: {$declination}
Distance to earth: {$distance} km

Is waxing moon: {$isWaxingMoon}
Illuminated fraction: {$illuminatedFraction}%
Position angle of bright limb: {$positionAngleOfBrightLimb}°

The moon seen from observer's location
Location: {$location->getLatitude()}°, {$location->getLongitude()}°
Azimuth: {$azimuth}° (apparent)
Altitude: {$altitude}° (apparent)

END;
