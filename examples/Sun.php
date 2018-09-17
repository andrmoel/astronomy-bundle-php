<?php

include __DIR__ . '/../vendor/autoload.php';

use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Sun;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

// Berlin
$lat = AngleUtil::angle2dec('52°31\'27.73"');
$lon = AngleUtil::angle2dec('13°24\'37.91"');
$location = new Location($lat, $lon);

// Time of interest
$dateTime = new DateTime('2018-01-10 12:00:00');
$toi = new TimeOfInterest($dateTime);

// Create moon
$sun = new Sun($toi);

// Get moon's position in sky
$localHorizontalCoordinates = $sun->getLocalHorizontalCoordinates($location);
$azimuth = $localHorizontalCoordinates->getAzimuth();
$azimuth = AngleUtil::dec2angle($azimuth);
$altitude = $localHorizontalCoordinates->getAltitude();
$altitude = AngleUtil::dec2angle($altitude);
$distanceAu = $sun->getRadiusVector();
$distance = $sun->getDistanceToEarth();

echo <<<END
Date: {$toi->getDateTime()->format('Y-m-d H:i:s')}
Azimuth: {$azimuth}
Altitude: {$altitude}
Distance to earth: {$distanceAu} AU ({$distance} km)

END;
