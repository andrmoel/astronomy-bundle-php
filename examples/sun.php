<?php

include __DIR__ . '/../vendor/autoload.php';

use Andrmoel\AstronomyBundle\AstronomicalObjects\Sun;
use Andrmoel\AstronomyBundle\Calculations\SunCalc;
use Andrmoel\AstronomyBundle\Corrections\GeocentricEclipticalSphericalCorrections;
use Andrmoel\AstronomyBundle\Corrections\LocalHorizontalCorrections;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

date_default_timezone_set('UTC');

// Berlin
$location = new Location(52.524, 13.411);

// Create sun
$toi = new TimeOfInterest(new DateTime('2019-05-15 21:07:00'));
$sun = new Sun($toi);

// Ecliptical spherical coordinates
$geoEclSphCoordinates = $sun->getGeocentricEclipticalSphericalCoordinates();

$correctionsEcl = new GeocentricEclipticalSphericalCorrections($toi);
$geoEclSphCoordinates = $correctionsEcl->correctCoordinates($geoEclSphCoordinates);

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

//$corrections = new LocalHorizontalCorrections();
//$localHorizontalCoordinates = $corrections->correctAtmosphericRefraction($localHorizontalCoordinates);

$azimuth = $localHorizontalCoordinates->getAzimuth();
$azimuth = AngleUtil::dec2angle(AngleUtil::normalizeAngle($azimuth));
$altitude = $localHorizontalCoordinates->getAltitude();
$altitude = AngleUtil::dec2angle($altitude);
$distanceAu = SunCalc::getRadiusVector($toi->getJulianCenturiesFromJ2000());
$distance = SunCalc::getDistanceToEarth($toi->getJulianCenturiesFromJ2000());

$culmination = $sun->getUpperCulmination($location);
var_dump($culmination);

echo <<<END
+------------------------------------
| Sun
+------------------------------------
Date: {$toi->getDateTime()->format('Y-m-d H:i:s')} UTC

Ecliptical longitude: {$eclLon}
Ecliptical latitude: {$eclLat}
Right ascension: {$rightAscension}
Declination: {$declination}
Distance to earth: {$distanceAu} AU ({$distance} km)

The sun seen from observer's location
Location: {$location->getLatitude()}°, {$location->getLongitude()}°
Azimuth: {$azimuth} (apparent)
Altitude: {$altitude} (apparent)
Sunrise: {$sun->getSunrise($location)->getDateTime()->format('Y-m-d H:i:s')} UTC
Culmination: {$culmination->getDateTime()->format('Y-m-d H:i:s')} UTC

END;
