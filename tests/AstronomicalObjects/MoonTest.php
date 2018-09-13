<?php

namespace Andrmoel\AstronomyBundle\Tests\AstronomicalObjects;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Earth;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Moon;
use Andrmoel\AstronomyBundle\Coordinates\EclipticalCoordinates;
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

        $this->assertEquals(136.9623, round($D, 4));
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

        $this->assertEquals(229.2784, round($M, 4));
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

        $this->assertEquals(143.4079, round($F, 4));
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

        $this->assertEquals(368410, round($distance));
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

        $this->assertEquals(-3.2291, round($latitude, 4));
        $this->assertEquals(133.1626, round($longitude, 4));
    }


    public function testGetLocalHorizontalCoordinates()
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

        $this->assertEquals(134.6832, round($rightAscension, 4));
        $this->assertEquals(13.7670, round($declination, 4)); // TODO Failed...
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

        // TODO Failed...
        $this->assertEquals(0.68, round($illuminatedFraction, 2));
    }
}
