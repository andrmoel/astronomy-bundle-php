<?php

include __DIR__ . '/../vendor/autoload.php';

use Andrmoel\AstronomyBundle\AstronomicalObjects\Sun;
use Andrmoel\AstronomyBundle\Calculations\SunCalc;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

date_default_timezone_set('UTC');

// Berlin
$location = Location::create(52.524, 13.411);

// Create sun
$sun = Sun::create();

// Ecliptical spherical coordinates
$geoEclSphCoordinates = $sun->getGeocentricEclipticalSphericalCoordinates();

$eclLon = $geoEclSphCoordinates->getLongitude();
$eclLon = AngleUtil::dec2angle($eclLon);
$eclLat = $geoEclSphCoordinates->getLatitude();
$eclLat = AngleUtil::dec2angle($eclLat);
$radiusVector = $geoEclSphCoordinates->getRadiusVector();

// Equatorial coordinates
$geoEqaCoordinates = $sun->getGeocentricEquatorialSphericalCoordinates();

$rightAscension = $geoEqaCoordinates->getRightAscension();
$rightAscension = AngleUtil::dec2time($rightAscension);
$declination = $geoEqaCoordinates->getDeclination();
$declination = AngleUtil::dec2angle($declination);

// Local horizontal coordinates
$localHorizontalCoordinates = $sun->getLocalHorizontalCoordinates($location);

$azimuth = $localHorizontalCoordinates->getAzimuth();
$azimuth = AngleUtil::dec2angle(AngleUtil::normalizeAngle($azimuth));
$altitude = $localHorizontalCoordinates->getAltitude();
$altitude = AngleUtil::dec2angle($altitude);
$distance = $sun->getDistanceToEarth();
$distanceAu = SunCalc::getRadiusVector($sun->getTimeOfInterest()->getJulianCenturiesFromJ2000());

// Rise, set and upper culmination
$rise = $sun->getSunrise($location);
$culmination = $sun->getUpperCulmination($location);
$set = $sun->getSunset($location);

echo <<<END
+------------------------------------
| Sun
+------------------------------------
Date: {$sun->getTimeOfInterest()} UTC

Ecliptical longitude: {$eclLon}
Ecliptical latitude: {$eclLat}
Right ascension: {$rightAscension}
Declination: {$declination}
Distance to earth: {$distance} kn ({$distanceAu} AU)

The sun seen from observer's location
Location: {$location->getLatitude()}°, {$location->getLongitude()}°
Azimuth: {$azimuth} (apparent)
Altitude: {$altitude} (apparent)
Sunrise: {$rise->getDateTime()->format('Y-m-d H:i:s')} UTC
Culmination: {$culmination->getDateTime()->format('Y-m-d H:i:s')} UTC
Sunset: {$set->getDateTime()->format('Y-m-d H:i:s')} UTC

END;
