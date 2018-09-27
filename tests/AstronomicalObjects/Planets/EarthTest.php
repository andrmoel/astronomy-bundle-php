<?php

namespace Andrmoel\AstronomyBundle\Tests\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Earth;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;
use PHPUnit\Framework\TestCase;

class EarthTest extends TestCase
{
    /**
     * Meeus 33.a
     */
    public function testGetHeliocentricEclipticalSphericalCoordinates()
    {
        $toi = new TimeOfInterest(new \DateTime('1992-12-20 00:00:00'));

        $earth = new Earth($toi);
        $helEclSphCoordinates = $earth->getHeliocentricEclipticalSphericalCoordinates();

        $L = $helEclSphCoordinates->getLongitude();
        $B = $helEclSphCoordinates->getLatitude();
        $R = $helEclSphCoordinates->getRadiusVector();

        $this->assertEquals(88.35700, round($L, 5));
        $this->assertEquals(0.00017, round($B, 5));
        $this->assertEquals(0.983824, round($R, 6));
    }

    /**
     * Meeus 33.a
     */
    public function testGetHeliocentricEclipticalRectangularCoordinates()
    {
        $toi = new TimeOfInterest(new \DateTime('1992-12-20 00:00:00'));

        $earth = new Earth($toi);
        $helEclSphCoordinates = $earth->getHeliocentricEclipticalRectangularCoordinates();

        $X = $helEclSphCoordinates->getX();
        $Y = $helEclSphCoordinates->getY();
        $Z = $helEclSphCoordinates->getZ();

        $this->assertEquals(0.028208, round($X, 6));
        $this->assertEquals(0.983420, round($Y, 6));
        $this->assertEquals(3.0E-6, round($Z, 6));
    }

    /**
     * Meeus 22.a
     */
    public function testGetMeanAnomaly()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(1987, 4, 10, 0, 0, 0);

        $earth = new Earth($toi);
        $M = $earth->getMeanAnomaly();

        $this->assertEquals(94.9806, round($M, 4));
    }

    /**
     * Meeus 25.a
     */
    public function testGetEccentricity()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(1992, 10, 12, 0, 0, 0);

        $earth = new Earth($toi);
        $e = $earth->getEccentricity();

        $this->assertEquals(0.016711669, round($e, 9));
    }

    /**
     * Meeus 22.a
     */
    public function testGetNutationInLongitude()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(1987, 4, 10, 0, 0, 0);

        $earth = new Earth($toi);
        $phi = $earth->getNutationInLongitude();

        $this->assertStringStartsWith('-0°0\'3.788', AngleUtil::dec2angle($phi));
    }

    /**
     * Meeus 22.a
     */
    public function testGetNutationInObliquity()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(1987, 4, 10, 0, 0, 0);

        $earth = new Earth($toi);
        $eps = $earth->getNutationInObliquity();

        $this->assertStringStartsWith('0°0\'9.442', AngleUtil::dec2angle($eps));
    }

    /**
     * Meeus 22.a
     */
    public function testGetMeanObliquityOfEcliptic()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(1987, 4, 10, 0, 0, 0);

        $earth = new Earth($toi);
        $e0 = $earth->getMeanObliquityOfEcliptic();

        $this->assertEquals(23.44095, round($e0, 5));
    }

    /**
     * Meeus 22.a
     */
    public function testGetObliquityOfEcliptic()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(1987, 4, 10, 0, 0, 0);

        $earth = new Earth($toi);
        $e = $earth->getObliquityOfEcliptic();

        $this->assertEquals(23.44357, round($e, 5));
    }

    public function testGetDistance()
    {
        // Berlin
        $lat = 52.518611;
        $lon = 13.408333;
        $location1 = new Location($lat, $lon);

        // Munich
        $lat = 48.137222;
        $lon = 11.575556;
        $location2 = new Location($lat, $lon);

        $distance = Earth::getDistance($location1, $location2);

        $this->assertEquals(504.50, round($distance, 2));
    }
}
