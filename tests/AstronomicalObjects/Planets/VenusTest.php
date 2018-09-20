<?php

namespace Andrmoel\AstronomyBundle\Tests\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Venus;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use PHPUnit\Framework\TestCase;

class VenusTest extends TestCase
{
    /**
     * Meeus 33.a
     */
    public function testGetHeliocentricEclipticalSphericalCoordinates()
    {
        $toi = new TimeOfInterest(new \DateTime('1992-12-20 00:00:00'));
        $venus = new Venus($toi);

        $helEclSphCoordinates = $venus->getHeliocentricEclipticalSphericalCoordinates();

        $L = $helEclSphCoordinates->getLongitude();
        $B = $helEclSphCoordinates->getLatitude();
        $R = $helEclSphCoordinates->getRadiusVector();

        $this->assertEquals(26.11428, round($L, 5));
        $this->assertEquals(-2.62070, round($B, 5));
        $this->assertEquals(0.724603, round($R, 6));
    }

    /**
     * Meeus 33.a
     */
    public function testGetHeliocentricEclipticalSphericalCoordinatesLightTimeCorrected()
    {
        $toi = new TimeOfInterest(new \DateTime('1992-12-20 00:00:00'));
        $venus = new Venus($toi);

        $helEclSphCoordinates = $venus->getHeliocentricEclipticalSphericalCoordinatesLightTimeCorrected();

        $L = $helEclSphCoordinates->getLongitude();
        $B = $helEclSphCoordinates->getLatitude();
        $R = $helEclSphCoordinates->getRadiusVector();

        $this->assertEquals(26.10587, round($L, 5));
        $this->assertEquals(-2.62102, round($B, 5));
        $this->assertEquals(0.724604, round($R, 6));
    }
}
