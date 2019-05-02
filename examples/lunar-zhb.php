<?php

date_default_timezone_set('UTC');

include __DIR__ . '/../vendor/autoload.php';

// Berlin
$lat = \Andrmoel\AstronomyBundle\Utils\AngleUtil::angle2dec('52°32\'16.73"');
$lon = \Andrmoel\AstronomyBundle\Utils\AngleUtil::angle2dec('13°24\'40.91"');

$toi = new \Andrmoel\AstronomyBundle\TimeOfInterest();
$toi->setTime(2019, 5, 2, 0, 0, 0);
$location = new \Andrmoel\AstronomyBundle\Location(52.51345, 13.42632);

$cha = new \Andrmoel\AstronomyBundle\Halos\CircumHorizontalArc();
$days = $cha->getDaysOfVisiblity(2019, $location);

var_dump($days);
