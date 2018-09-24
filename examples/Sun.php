<?php

include __DIR__ . '/../vendor/autoload.php';

use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Sun;
use Andrmoel\AstronomyBundle\Corrections\GeocentricEclipticalSphericalCorrections;
use Andrmoel\AstronomyBundle\Corrections\GeocentricEquatorialCorrections;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

// Berlin
$lat = AngleUtil::angle2dec('52°31\'27.73"');
$lon = AngleUtil::angle2dec('13°24\'37.91"');
$location = new Location($lat, $lon);

// Time of interest
$dateTime = new \DateTime('1992-10-13 00:00:00');
$toi = new TimeOfInterest($dateTime);

// Create sun
$sun = new Sun($toi);

// Get sun's position in sky
$geoEclSphCoordinates = $sun->getGeocentricEclipticalSphericalCoordinates();
$geoEqaCoordinates = $sun->getGeocentricEquatorialCoordinates();
$localHorizontalCoordinates = $sun->getLocalHorizontalCoordinates($location);

// Correct coordinates
$correctionsEcl = new GeocentricEclipticalSphericalCorrections($toi);
$geoEclSphCoordinates = $correctionsEcl->correctCoordinates($geoEclSphCoordinates);

$correctionsEqu = new GeocentricEquatorialCorrections($toi);
$geoEqaCoordinates = $correctionsEqu->correctCoordinates($geoEqaCoordinates);

$lon = $geoEclSphCoordinates->getLongitude();
$lon = AngleUtil::dec2angle($lon);
$lat = $geoEclSphCoordinates->getLatitude();
$lat = AngleUtil::dec2angle($lat);
$radiusVector = $geoEclSphCoordinates->getRadiusVector();

$rightAscension = $geoEqaCoordinates->getRightAscension();
$rightAscension = AngleUtil::dec2time($rightAscension);
$declination = $geoEqaCoordinates->getDeclination();
$declination = AngleUtil::dec2angle($declination);

$azimuth = $localHorizontalCoordinates->getAzimuth();
$azimuth = AngleUtil::dec2angle($azimuth);
$altitude = $localHorizontalCoordinates->getAltitude();
$altitude = AngleUtil::dec2angle($altitude);
$distanceAu = $sun->getRadiusVector();
$distance = $sun->getDistanceToEarth();

echo <<<END
Date: {$toi->getDateTime()->format('Y-m-d H:i:s')}
Ecliptical longitude: {$lon}
Ecliptical latitude: {$lat}
Right ascension: {$rightAscension}
Declination: {$declination}
Distance to earth: {$distanceAu} AU ({$distance} km)

Azimuth: {$azimuth}
Altitude: {$altitude}

END;
