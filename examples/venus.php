<?php

include __DIR__ . '/../vendor/autoload.php';

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Venus;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;

date_default_timezone_set('UTC');

// Berlin
$location = new Location(52.524, 13.411);

// Create sun
$toi = new TimeOfInterest(new DateTime('1992-12-20 00:00:00'));

$venus = new Venus($toi);

var_dump($venus->getHeliocentricEclipticalSphericalCoordinates());
