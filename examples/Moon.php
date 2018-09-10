<?php

include __DIR__ . '/../vendor/autoload.php';

use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Earth;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Moon;

// Berlin
$lat = 52.518611;
$lon = 13.408333;
$location = new Location($lat, $lon);

// Time of interest
$toi = new TimeOfInterest();
$toi->setTime(2018, 1, 10, 5, 0, 0);

// Create earth
$earth = new Earth();
$earth->setTimeOfInterest($toi);
$earth->setLocation($location);

// Create moon
$moon = new Moon();
$moon->setTimeOfInterest($toi);

// Get moon's altitude
$horizontalCoordinates = $moon->getHorizontalCoordinates($earth);

echo <<<END
Date: {$toi->getTimeString()}
Azimuth: {$horizontalCoordinates->getAzimuth()}
Altitude: {$horizontalCoordinates->getAltitude()}
Is waxing moon: {$moon->isWaxingMoon()}
Illuminated fraction: {$moon->getIlluminatedFraction()}
END;
