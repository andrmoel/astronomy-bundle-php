<?php

namespace Andrmoel\AstronomyBundle\Tests\AstronomicalObjects;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Earth;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Moon;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Sun;
use Andrmoel\AstronomyBundle\Coordinates\EclipticalCoordinates;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use PHPUnit\Framework\TestCase;

class EarthTest extends TestCase
{
    /**
     * Meeus 22.a
     */
    public function testGetMeanAnomaly()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(1987, 4, 10, 0, 0, 0);

        $earth = new Earth($toi);
        $M = $earth->getMeanAnomaly();

        var_dump($M);die();
    }

    /**
     * Meeus 22.a
     */
//    public function testGetGeometricMeanLongitude()
//    {
//        $toi = new TimeOfInterest();
//        $toi->setTime(1987, 4, 10, 0, 0, 0);
//
//        $earth = new Earth($toi);
//        $M = $earth->getMeanAnomaly();
//
//        var_dump($M);
//    }
}
