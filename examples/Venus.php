<?php

include __DIR__ . '/../vendor/autoload.php';

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Venus;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

// Berlin
$lat = AngleUtil::angle2dec('52°31\'27.73"');
$lon = AngleUtil::angle2dec('13°24\'37.91"');
$location = new Location($lat, $lon);

// Time of interest
$dateTime = new DateTime('2018-01-10 05:00:00');
$toi = new TimeOfInterest($dateTime);

// Create moon
$moon = new Venus($toi);

// Get moon's position in sky
$localHorizontalCoordinates = $moon->getLocalHorizontalCoordinates($location);
$azimuth = $localHorizontalCoordinates->getAzimuth();
$azimuth = AngleUtil::dec2angle($azimuth);
$altitude = $localHorizontalCoordinates->getAltitude();
$altitude = AngleUtil::dec2angle($altitude);
$isWaxingMoon = $moon->isWaxingMoon() ? 'yes' : 'no';
$illuminatedFraction = $moon->getIlluminatedFraction();
$positionAngleOfBrightLimb = $moon->getPositionAngleOfMoonsBrightLimb();

echo <<<END
+------------------------------------
| Moon
+------------------------------------
Date: {$toi->getDateTime()->format('Y-m-d H:i:s')}
Azimuth: {$azimuth}
Altitude: {$altitude}
Is waxing moon: {$isWaxingMoon}
Illuminated fraction: {$illuminatedFraction}
Position angle of bright limb: {$positionAngleOfBrightLimb}

END;
