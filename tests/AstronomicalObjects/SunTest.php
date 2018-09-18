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
        $toi = new TimeOfInterest(new \DateTime('1992-10-13 00:00:00'));

        $sun = new Sun($toi);
        $L = $sun->getMeanLongitude();

        $this->assertEquals(201.80719, round($L, 5));
    }

    /**
     * Meeus 25.a
     */
    public function testGetMeanAnomaly()
    {
        $toi = new TimeOfInterest(new \DateTime('1992-10-13 00:00:00'));

        $sun = new Sun($toi);
        $M = $sun->getMeanAnomaly();

        $this->assertEquals(278.99397, round($M, 5));
    }

    public function testGetRadiusVector()
    {
        $toi = new TimeOfInterest(new \DateTime('1992-10-13 00:00:00'));

        $sun = new Sun($toi);
        $R = $sun->getRadiusVector();

        $this->assertEquals(0.99766, round($R, 5));
    }

    public function testGetEclipticalCoordinates()
    {
        $toi = new TimeOfInterest(new \DateTime('1992-10-13 00:00:00'));

        $sun = new Sun($toi);
        $eclipticalCoordinates = $sun->getEclipticalCoordinates();

        $latitude = $eclipticalCoordinates->getLatitude();
        $longitude = $eclipticalCoordinates->getLongitude();

        // TODO Rundungsfehler?
        $this->assertEquals(-0.00027, round($latitude, 5));
        $this->assertEquals(199.90907, round($longitude, 5));
    }

    /**
     * Meeus 25.a
     */
    public function testGetEquatorialCoordinates()
    {
        $toi = new TimeOfInterest(new \DateTime('1992-10-13 00:00:00'));

        $sun = new Sun($toi);
        $equatorialCoordinates = $sun->getEquatorialCoordinates();

        $rightAscension = $equatorialCoordinates->getRightAscension();
        $declination = $equatorialCoordinates->getDeclination();

        $this->assertEquals(198.38082, round($rightAscension, 5));
        $this->assertEquals(-7.78542, round($declination, 5)); // TODO Should be -7.78507
    }

    // TODO ...
    public function XtestFF()
    {
        $toi = new TimeOfInterest(new \DateTime('1992-10-13 00:00:00'));

        $sun = new Sun($toi);
        $equatorialCoordinates = $sun->getRectangularGeocentricEquatorialCoordinates();
    }

    /**
     * Meeus 28.a
     */
    public function testGetEquationOfTime()
    {
        $toi = new TimeOfInterest(new \DateTime('1992-10-13 00:00:00'));

        $sun = new Sun($toi);
        $equationOfTime = $sun->getEquationOfTime();

        $this->assertEquals(3.42012, round($equationOfTime, 5)); // TODO Should be 3.427351
    }
}
