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
    public function testGetHeliocentricCoordinates()
    {
        $toi = new TimeOfInterest(new \DateTime('1992-12-20 00:00:00'));

        $earth = new Earth($toi);
        $heliocentricCoordinates = $earth->getHeliocentricCoordinates();

        $L = $heliocentricCoordinates->getEclipticalLongitude();
        $B = $heliocentricCoordinates->getEclipticalLatitude();
        $R = $heliocentricCoordinates->getRadiusVector();

        $this->assertEquals(88.35704, round($L, 5));
        $this->assertEquals(0.00014, round($B, 5));
        $this->assertEquals(0.983824, round($R, 6));
    }
}
