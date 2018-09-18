<?php

namespace Andrmoel\AstronomyBundle\Tests\AstronomicalObjects;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Venus;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use PHPUnit\Framework\TestCase;

class VenusTest extends TestCase
{
    /**
     * Meeus 32.a
     */
    public function testGetEclipticalLongitude()
    {
        $toi = new TimeOfInterest(new \DateTime('1992-12-20 00:00:00'));
        $venus = new Venus($toi);

        $L = $venus->getEclipticalLongitude();

        $this->assertEquals(26.11428, round($L, 5));
    }

    /**
     * Meeus 32.a
     */
    public function testGetEclipticalLatitude()
    {
        $toi = new TimeOfInterest(new \DateTime('1992-12-20 00:00:00'));
        $venus = new Venus($toi);

        $B = $venus->getEclipticalLatitude();

        $this->assertEquals(-2.62070, round($B, 5));
    }

    /**
     * Meeus 32.a
     */
    public function testGetRadiusVector()
    {
        $toi = new TimeOfInterest(new \DateTime('1992-12-20 00:00:00'));
        $venus = new Venus($toi);

        $R = $venus->getRadiusVector();

        $this->assertEquals(0.724603, round($R, 6));
    }
}
