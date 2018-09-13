<?php

namespace Andrmoel\AstronomyBundle\Tests\AstronomicalObjects;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Earth;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Moon;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Sun;
use Andrmoel\AstronomyBundle\Coordinates\EclipticalCoordinates;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use PHPUnit\Framework\TestCase;

class SunTest extends TestCase
{
    /**
     * Meeus 25.a
     */
    public function testGetGeometricMeanLongitude()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(1992, 10, 13, 0, 0, 0);

        $sun = new Sun($toi);
        $L = $sun->getGeometricMeanLongitude();

        $this->assertEquals(-2318.19280, round($L, 5));
    }

    /**
     * Meeus 25.a
     */
    public function testGetMeanAnomaly()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(1992, 10, 13, 0, 0, 0);

        $sun = new Sun($toi);
        $M = $sun->getMeanAnomaly();

        $this->assertEquals(-2241.00603, round($M, 5));
    }

    /**
     * Meeus 25.a
     */
    public function testGetEquatorialCoordinates()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(1992, 10, 13, 0, 0, 0);

        $sun = new Sun($toi);
        $equatorialCoordinates = $sun->getEquatorialCoordinates();
//
//        var_dump($equatorialCoordinates->getRightAscension(), $equatorialCoordinates->getDeclination());die("DDD");
    }
}
