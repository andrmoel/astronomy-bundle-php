<?php

include __DIR__ . '/../vendor/autoload.php';

use Andrmoel\AstronomyBundle\AstronomicalObjects\Moon;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;

// Berlin
$lat = 52.518611;
$lon = 13.408333;
$location = new Location($lat, $lon);

// Time of interest
$dateTime = new DateTime('2018-01-10 05:00:00');
$toi = new TimeOfInterest($dateTime);

// Create moon
$moon = new Moon($toi);

$equatorialCoordinates = $moon->getEquatorialCoordinates();

var_dump($equatorialCoordinates->getRightAscension(), $equatorialCoordinates->getDeclination());

// Get moon's altitude
$localHorizontalCoordinates = $moon->getLocalHorizontalCoordinates($location);

echo <<<END
Date: {$toi->getDateTime()->format('Y-m-d H:i:s')}
Azimuth: {$localHorizontalCoordinates->getAzimuth()}
Altitude: {$localHorizontalCoordinates->getAltitude()}
Is waxing moon: {$moon->isWaxingMoon()}
Illuminated fraction: {$moon->getIlluminatedFraction()}
END;
