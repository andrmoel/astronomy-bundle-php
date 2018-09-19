<?php

namespace Andrmoel\AstronomyBundle\Tests\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Earth;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use PHPUnit\Framework\TestCase;

class EarthTest extends TestCase
{
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

        $this->assertEquals(0.028207, round($X, 6));
        $this->assertEquals(0.983420, round($Y, 6));
        $this->assertEquals(2.0E-6, round($Z, 6));
    }

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

        $this->assertEquals(88.35704, round($L, 5));
        $this->assertEquals(0.00014, round($B, 5));
        $this->assertEquals(0.983824, round($R, 6));
    }
}
