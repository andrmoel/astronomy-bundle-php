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

$helEclSphCoord = $venus->getHeliocentricEclipticalSphericalCoordinates();
$lat = $helEclSphCoord->getLatitude();
$lon = $helEclSphCoord->getLongitude();
$r = $helEclSphCoord->getRadiusVector();

$helEclRecCoord = $venus->getHeliocentricEclipticalRectangularCoordinates();
$x = $helEclRecCoord->getX();
$y = $helEclRecCoord->getY();
$z = $helEclRecCoord->getZ();

var_dump($lat, $lon, $r);
var_dump($x, $y, $z);
