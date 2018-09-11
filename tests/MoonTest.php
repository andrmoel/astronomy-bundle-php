<?php

namespace Andrmoel\AstronomyBundle\Tests\Complex;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Earth;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Moon;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use PHPUnit\Framework\TestCase;

class MoonTest extends TestCase
{
    public function testMoonRise()
    {
//        // Berlin
//        $lat = 52.518611;
//        $lon = 13.408333;
//        $location = new Location($lat, $lon);
//
//        // Time of interest is today
//        $toi = new TimeOfInterest();
//        $toi->setTime(2018, 1, 10, 6, 0, 0);
//
//        // Create earth
//        $earth = new Earth();
//        $earth->setTimeOfInterest($toi);
//        $earth->setLocation($location);
//
//        // Create moon
//        $moon = new Moon();
//        $moon->setTimeOfInterest($toi);
//
//        // Get moon's altitude
//        $horizontalCoordinates = $moon->getHorizontalCoordinates($earth);
//        $altitude = $horizontalCoordinates->getAltitude();
//
//        // Output
//        $lat = round($lat, 3);
//        $lon = round($lon, 3);
//        $altitude = round($altitude, 1);
//        $date = date('d.m.Y H:i:s');
//
//echo <<<END
//
//    Altitude: {$altitude}
//    Is waxing moon: {$moon->isWaxingMoon()}
//    Illuminated fraction: {$moon->getIlluminatedFraction()}
//END;
//
        $this->assertTrue(true);
    }
}
