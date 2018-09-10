<?php

namespace Andrmoel\AstronomyBundle\Examples;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Earth;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Moon;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;

class MoonAltitude
{
    public function run()
    {
        // Berlin
        $lat = 52.518611;
        $lon = 13.408333;
        $location = new Location($lat, $lon);

        // Time of interest is today
        $toi = new TimeOfInterest();
        $toi->setUnixTime(time());

        // Create earth
        $earth = new Earth();
        $earth->setTimeOfInterest($toi);
        $earth->setLocation($location);

        // Create moon
        $moon = new Moon();
        $moon->setTimeOfInterest($toi);

        // Get sun's altitude
        $horizontalCoordinates = $moon->getHorizontalCoordinates($earth);
        $altitude = $horizontalCoordinates->getAltitude();

        // Output
        $lat = round($lat, 3);
        $lon = round($lon, 3);
        $altitude = round($altitude, 1);
        $date = date('d.m.Y H:i:s');

        echo 'The moon\'s altitude at ' . $lat . '°, ' . $lon . '° at ' . $date . ' is ' . $altitude . '°.';
    }
}
