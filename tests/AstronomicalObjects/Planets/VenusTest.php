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
    public function testGetHeliocentricCoordinates()
    {
        $toi = new TimeOfInterest(new \DateTime('1992-12-20 00:00:00'));
        $venus = new Venus($toi);

        $heliocentricCoordinates = $venus->getHeliocentricCoordinates();

        $L = $heliocentricCoordinates->getEclipticalLongitude();
        $B = $heliocentricCoordinates->getEclipticalLatitude();
        $R = $heliocentricCoordinates->getRadiusVector();

        $this->assertEquals(26.11428, round($L, 5));
        $this->assertEquals(-2.62070, round($B, 5));
        $this->assertEquals(0.724603, round($R, 6));
    }
}
