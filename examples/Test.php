<?php

include __DIR__ . '/../vendor/autoload.php';

use \Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Planet;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Mercury;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Venus;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Earth;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Mars;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Jupiter;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Saturn;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Uranus;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Neptune;
use Andrmoel\AstronomyBundle\Corrections\GeocentricEquatorialCorrections;
use Andrmoel\AstronomyBundle\Corrections\GeocentricEclipticalSphericalCorrections;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

// Berlin
$lat = AngleUtil::angle2dec('52°31\'27.73"');
$lon = AngleUtil::angle2dec('13°24\'37.91"');
$location = new Location($lat, $lon);

// Time of interest
$toi = new TimeOfInterest(new DateTime('1992-12-20 00:00:00'));

$venus = new Venus($toi);

// Get geocentric ecliptical coordinates
$geoEclSphCoordinates = $venus
    ->getApparentHeliocentricEclipticalSphericalCoordinates()
    ->getGeocentricEclipticalSphericalCoordinates($toi);

// Corrections
$corrections = new GeocentricEclipticalSphericalCorrections($toi);
$geoEclSphCoordinates = $corrections->correctCoordinates($geoEclSphCoordinates);

$geoEquCorodinates = $geoEclSphCoordinates->getGeocentricEquatorialCoordinates($toi);

$rightAscension = $geoEquCorodinates->getRightAscension();
$declination = $geoEquCorodinates->getDeclination();
var_dump(AngleUtil::dec2time($rightAscension), AngleUtil::dec2angle($declination));

// Get geocentric equatorial coordinates
$geoEquCorodinates = $geoEclSphCoordinates
    ->getGeocentricEquatorialCoordinates($toi);

// Corrections
$corrections = new GeocentricEquatorialCorrections($toi);
$geoEquCorodinates = $corrections->correctCoordinates($geoEquCorodinates);

$rightAscension = $geoEquCorodinates->getRightAscension();
$declination = $geoEquCorodinates->getDeclination();
var_dump(AngleUtil::dec2time($rightAscension), AngleUtil::dec2angle($declination));

//echo <<<END
//+------------------------------------
//| {$planetName}
//+------------------------------------
//Date: {$toi->getDateTime()->format('Y-m-d H:i:s')}
//Ecliptical longitude: {$eclLongitude}
//Ecliptical latitude: {$eclLatitude}
//Right ascending: {$rightAscension}
//Declination: {$declination}
//
//Position: {$lat}°, {$lon}°
//Azimuth: {$azimuth}
//Altitude: {$altitude}
//
//END;
