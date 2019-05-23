<?php

include __DIR__ . '/../vendor/autoload.php';

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Venus;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;

date_default_timezone_set('UTC');

// Berlin
$location = new Location(52.524, 13.411);

$toi = new TimeOfInterest(new DateTime('1992-12-20 00:00:00'));

$venus = new Venus($toi);

$T = $toi->getJulianCenturiesFromJ2000();
$et = \Andrmoel\AstronomyBundle\Calculations\TimeCalc::getEquationOfTimeInDegrees($T);
var_dump(\Andrmoel\AstronomyBundle\Utils\AngleUtil::dec2time($et));
die();
var_dump($toi->getJulianDay());
var_dump($venus->getHeliocentricEclipticalRectangularCoordinates());

$coord = $venus->getHeliocentricEclipticalSphericalCoordinates();
var_dump(
    \Andrmoel\AstronomyBundle\Utils\AngleUtil::dec2angle($coord->getLatitude()),
    \Andrmoel\AstronomyBundle\Utils\AngleUtil::dec2angle($coord->getLongitude())
);

var_dump("----------------------------");

$coord = $venus->getGeocentricEclipticalRectangularCoordinates();
var_dump($coord);