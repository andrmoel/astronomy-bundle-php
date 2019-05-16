<?php

namespace Andrmoel\AstronomyBundle\Tests\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Venus;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use PHPUnit\Framework\TestCase;

class VenusTest extends TestCase
{
    /**
     * @test
     * Meeus 33.a
     */
    public function getHeliocentricEclipticalSphericalCoordinatesTest()
    {
        $toi = new TimeOfInterest(new \DateTime('1992-12-20 00:00:00'));
        $venus = new Venus($toi);

        $helEclSphCoordinates = $venus->getHeliocentricEclipticalSphericalCoordinates();

        $L = $helEclSphCoordinates->getLongitude();
        $B = $helEclSphCoordinates->getLatitude();
        $R = $helEclSphCoordinates->getRadiusVector();

        $this->assertEquals(26.11412, round($L, 5));
        $this->assertEquals(-2.62060, round($B, 5));
        $this->assertEquals(0.724602, round($R, 6));
    }

    /**
     * @test
     * Meeus 33.a
     */
    public function getApparentHeliocentricEclipticalSphericalCoordinatesTest()
    {
        $toi = new TimeOfInterest(new \DateTime('1992-12-20 00:00:00'));
        $venus = new Venus($toi);

        $helEclSphCoordinates = $venus->getApparentHeliocentricEclipticalSphericalCoordinates();

        $L = $helEclSphCoordinates->getLongitude();
        $B = $helEclSphCoordinates->getLatitude();
        $R = $helEclSphCoordinates->getRadiusVector();

        $this->assertEquals(26.10571, round($L, 5));
        $this->assertEquals(-2.62092, round($B, 5));
        $this->assertEquals(0.724602, round($R, 6));
    }

    public function getHeliocentricEclipticalRectangularCoordinatesTest()
    {
        $toi = new TimeOfInterest(new \DateTime('1992-12-20 00:00:00'));
        $venus = new Venus($toi);

        $helEclRecCoordinates = $venus->getHeliocentricEclipticalRectangularCoordinates();

        $X = $helEclRecCoordinates->getX();
        $Y = $helEclRecCoordinates->getY();
        $Z = $helEclRecCoordinates->getZ();

        // TODO ... evaluate
        $this->assertEquals(0.649953, round($X, 6));
        $this->assertEquals(0.318607, round($Y, 6));
        $this->assertEquals(-0.03313, round($Z, 6));
    }
}
