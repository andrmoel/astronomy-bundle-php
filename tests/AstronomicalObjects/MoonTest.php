<?php

namespace Andrmoel\AstronomyBundle\Tests\AstronomicalObjects;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Moon;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use PHPUnit\Framework\TestCase;

class MoonTest extends TestCase
{
    /**
     * Meeus 22.a
     */
    public function testGetMeanElongationFromSun()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(1987, 4, 10, 0, 0, 0);

        $moon = new Moon($toi);
        $D = $moon->getMeanElongationFromSun();

        $this->assertEquals(136.96215, round($D, 5));
    }

    /**
     * Meeus 22.a
     */
    public function testGetMeanAnomaly()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(1987, 4, 10, 0, 0, 0);

        $moon = new Moon($toi);
        $M = $moon->getMeanAnomaly();

        $this->assertEquals(229.27882, round($M, 5));
    }

    /**
     * Meeus 22.a
     */
    public function testGetArgumentOfLatitude()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(1987, 4, 10, 0, 0, 0);

        $moon = new Moon($toi);
        $F = $moon->getArgumentOfLatitude();

        $this->assertEquals(143.40809, round($F, 5));
    }

    /**
     * Meeus 47.a
     */
    public function testGetMeanLongitude()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(1992, 4, 12, 0, 0, 0);

        $moon = new Moon($toi);
        $L = $moon->getMeanLongitude();

        $this->assertEquals(134.290182, round($L, 6));
    }

    /**
     * Meeus 47.a
     */
    public function testGetDistanceToEarth()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(1992, 4, 12, 0, 0, 0);

        $moon = new Moon($toi);
        $distance = $moon->getDistanceToEarth();

        $this->assertEquals(368409.7, round($distance, 1));
    }

    /**
     * Meeus 47.a
     */
    public function testGetEquatorialHorizontalParallax()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(1992, 4, 12, 0, 0, 0);

        $moon = new Moon($toi);
        $distance = $moon->getEquatorialHorizontalParallax();

        $this->assertEquals(0.991990, round($distance, 6));
    }

    /**
     * Meeus 47.a
     */
    public function testGetEclipticalCoordinates()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(1992, 4, 12, 0, 0, 0);

        $moon = new Moon($toi);
        $eclipticalCoordinates = $moon->getEclipticalCoordinates();

        $latitude = $eclipticalCoordinates->getLatitude();
        $longitude = $eclipticalCoordinates->getLongitude();

        $this->assertEquals(-3.229126, round($latitude, 6));
        $this->assertEquals(133.167264, round($longitude, 6));
    }


    public function XtestGetLocalHorizontalCoordinates()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(1992, 4, 12, 0, 0, 0);

        $lat = 52.518611;
        $lon = 13.408333;
        $location = new Location($lat, $lon);

        $moon = new Moon($toi);
        $localHorizontalCoordinates = $moon->getLocalHorizontalCoordinates($location);

        $azimuth = $localHorizontalCoordinates->getAzimuth();
        $altitude = $localHorizontalCoordinates->getAltitude();

//        var_dump($azimuth, $altitude);die();

        // TODO Should be ... 212 / 47...

//        $this->assertEquals(-3.229126, round($latitude, 6));
//        $this->assertEquals(133.162655, round($longitude, 6));
    }


    /**
     * Meeus 47.a
     */
    public function testGetEquatorialCoordinates()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(1992, 4, 12, 0, 0, 0);

        $moon = new Moon($toi);
        $equatorialCoordinates = $moon->getEquatorialCoordinates();

        $rightAscension = $equatorialCoordinates->getRightAscension();
        $declination = $equatorialCoordinates->getDeclination();

        $this->assertEquals(134.68847, round($rightAscension, 5));
        $this->assertEquals(13.76837, round($declination, 5));
    }


    /**
     * Meeus 48.a
     */
    public function testGetIlluminatedFraction()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(1992, 4, 12, 0, 0, 0);

        $moon = new Moon($toi);
        $illuminatedFraction = $moon->getIlluminatedFraction();

        $this->assertEquals(0.68, round($illuminatedFraction, 2));
    }

    /**
     * Meeus 48.a
     */
    public function testGetPositionAngleOfMoonsBrightLimb()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(1992, 4, 12, 0, 0, 0);

        $moon = new Moon($toi);
        $x = $moon->getPositionAngleOfMoonsBrightLimb();

        $this->assertEquals(285.0, round($x, 1));
    }
}
