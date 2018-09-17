<?php

namespace Andrmoel\AstronomyBundle\Tests\AstronomicalObjects;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Sun;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use PHPUnit\Framework\TestCase;

class SunTest extends TestCase
{
    /**
     * Meeus 28.a
     */
    public function testGetMeanLongitude()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(1992, 10, 13, 0, 0, 0);

        $sun = new Sun($toi);
        $L = $sun->getMeanLongitude();

        $this->assertEquals(201.807193, round($L, 6));
    }

    /**
     * Meeus 25.a
     */
    public function XtestGetMeanAnomaly()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(1992, 10, 13, 0, 0, 0);

        $sun = new Sun($toi);
        $M = $sun->getMeanAnomaly();

        $this->assertEquals(278.99397, round($M, 5));
    }

    public function XtestGetRadiusVector()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(1992, 10, 13, 0, 0, 0);

        $sun = new Sun($toi);
        $R = $sun->getRadiusVector();

        $this->assertEquals(0.99766, round($R, 5));
    }

    /**
     * Meeus 25.a
     */
    public function XtestGetEquatorialCoordinates()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(1992, 10, 13, 0, 0, 0);

        $sun = new Sun($toi);
        $equatorialCoordinates = $sun->getEquatorialCoordinates();

        $rightAscension = $equatorialCoordinates->getRightAscension();
        $declination = $equatorialCoordinates->getDeclination();

        $this->assertEquals(198.38084, round($rightAscension, 5));
        $this->assertEquals(-7.78539, round($declination, 5)); // TODO Sollte -7.78507 sein
    }

    public function XtestFF()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(1992, 10, 13, 0, 0, 0);

        $sun = new Sun($toi);
        $equatorialCoordinates = $sun->getRectangularGeocentricEquatorialCoordinates();
    }
}
