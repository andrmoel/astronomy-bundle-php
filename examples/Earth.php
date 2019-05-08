<?php

include __DIR__ . '/../vendor/autoload.php';

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Earth;
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
$earth = new Earth($toi);

// Get moon's position in sky
$meanObliquityOfEcliptic = $earth->getMeanObliquityOfEcliptic();
$meanObliquityOfEcliptic = AngleUtil::dec2angle($meanObliquityOfEcliptic);
$obliquityOfEcliptic = $earth->getTrueObliquityOfEcliptic();
$obliquityOfEcliptic = AngleUtil::dec2angle($obliquityOfEcliptic);
$nutation = $earth->getNutationInObliquity();
$nutation = AngleUtil::dec2angle($nutation);

echo <<<END
+------------------------------------
| Earth
+------------------------------------
Date: {$toi->getDateTime()->format('Y-m-d H:i:s')}
Mean obliquity of ecliptic: {$meanObliquityOfEcliptic}
Obliquity of ecliptic: {$obliquityOfEcliptic}
Nutation on obliquity: {$nutation}

END;
