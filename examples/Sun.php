<?php

include __DIR__ . '/../vendor/autoload.php';

use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Earth;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Sun;

// Berlin
$lat = 52.518611;
$lon = 13.408333;
$location = new Location($lat, $lon);

$earth = new Earth();
$earth->setLocation($location);

// Time of interest
$toi = new TimeOfInterest();
$toi->setTime(1992, 10, 13, 0, 0, 0);

$sun = new Sun();
$sun->setTimeOfInterest($toi);

$equatorialCoordinates = $sun->getEquatorialCoordinates();
$geocentricCoordinates = $sun->getGeocentricCoordinates();

var_dump($equatorialCoordinates->getRightAscension(), $equatorialCoordinates->getDeclination());
var_dump($geocentricCoordinates->getX(), $geocentricCoordinates->getY(), $geocentricCoordinates->getZ());
