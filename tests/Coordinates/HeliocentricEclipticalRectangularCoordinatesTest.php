<?php

namespace Andrmoel\AstronomyBundle\Tests\Coordinates;

use Andrmoel\AstronomyBundle\Coordinates\HeliocentricEclipticalRectangularCoordinates;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use PHPUnit\Framework\TestCase;

class HeliocentricEclipticalRectangularCoordinatesTest extends TestCase
{
    public function testGetHeliocentricEclipticalSphericalCoordinates()
    {
        $X = 0.621794;
        $Y = -0.664905;
        $Z = -0.033138;

        $hcEclRecCoordinates = new HeliocentricEclipticalRectangularCoordinates($X, $Y, $Z);
        $hcRecSphCoordinates = $hcEclRecCoordinates->getHeliocentricEclipticalSphericalCoordinates();

        $L = $hcRecSphCoordinates->getLongitude();
        $B = $hcRecSphCoordinates->getLatitude();
        $R = $hcRecSphCoordinates->getRadiusVector();

        $this->assertEquals(313.08102, round($L, 5));
        $this->assertEquals(-2.08474, round($B, 5));
        $this->assertEquals(0.910947, round($R, 6));
    }

    public function testGetGeocentricEclipticalRectangularCoordinates()
    {
        $X = 0.649954;
        $Y = 0.318610;
        $Z = -0.033132;

        $toi = new TimeOfInterest(new \DateTime('1992-12-20 00:00:00'));

        $hcEclRecCoordinates = new HeliocentricEclipticalRectangularCoordinates($X, $Y, $Z);
        $geoEclRecCoordinates = $hcEclRecCoordinates->getGeocentricEclipticalRectangularCoordinates($toi);

        $X = $geoEclRecCoordinates->getX();
        $Y = $geoEclRecCoordinates->getY();
        $Z = $geoEclRecCoordinates->getZ();

        $this->assertEquals(0.621747, round($X, 6));
        $this->assertEquals(-0.66481, round($Y, 6));
        $this->assertEquals(-0.033134, round($Z, 6));
    }
}
