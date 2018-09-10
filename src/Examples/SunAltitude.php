<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 01.06.15
 * Time: 20:22
 */

namespace Andrmoel\AstronomyBundle\Examples;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Earth;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Sun;
use Andrmoel\AstronomyBundle\TimeOfInterest;

class SunAltitude
{
    /**
     * Run example
     */
    public function run()
    {
        // Berlin
        $lat = 52.518611;
        $lon = 13.408333;

        // Time of interest is today
        $toi = new TimeOfInterest();
        $toi->setUnixTime(time());

        // Create earth
        $earth = new Earth();
        $earth->setTimeOfInterest($toi);
        $earth->setLocation($lat, $lon);

        // Create sun
        $sun = new Sun();
        $sun->setTimeOfInterest($toi);

        // Get sun's altitude
        $horizontalCoordinates = $sun->getHorizontalCoordinates($earth);
        $altitude = $horizontalCoordinates->getAltitude();

        // Output
        $lat = round($lat, 3);
        $lon = round($lon, 3);
        $altitude = round($altitude, 1);
        $date = date('d.m.Y H:i:s');

        echo 'The sun\'s altitude at ' . $lat . '°, ' . $lon . '° at ' . $date . ' is ' . $altitude . '°.';
    }
}
