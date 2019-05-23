<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Coordinates\HeliocentricEclipticalRectangularCoordinates;
use PHPUnit\Framework\TestCase;

class HeliocentricEclipticalRectangularCoordinatesTest extends TestCase
{
    /**
     * @test
     */
    public function getHeliocentricEclipticalSphericalCoordinatesTest()
    {
        $x = 0.64995327095595;
        $y = 0.31860745636351;
        $z = -0.033130385747949;

        $helEclRecCoord = new HeliocentricEclipticalRectangularCoordinates($x, $y, $z);
        $helEclSphCoord = $helEclRecCoord->getHeliocentricEclipticalSphericalCoordinates();

        $lat = $helEclSphCoord->getLatitude();
        $lon = $helEclSphCoord->getLongitude();
        $r = $helEclSphCoord->getRadiusVector();

        $this->assertEquals(-2.620603, round($lat, 6));
        $this->assertEquals(26.11412, round($lon, 6));
        $this->assertEquals(0.724602, round($r, 6));
    }

    /**
     * @test
     * Meeus 33.a
     */
    public function getGeocentricEclipticalRectangularCoordinatesTest()
    {
        $T = -0.070321697467488;
        $x = 0.64995327095595;
        $y = 0.31860745636351;
        $z = -0.033130385747949;

        $helEclRecCoord = new HeliocentricEclipticalRectangularCoordinates($x, $y, $z);
        $geoEclRecCoord = $helEclRecCoord->getGeocentricEclipticalRectangularCoordinates($T);

        $X = $geoEclRecCoord->getX();
        $Y = $geoEclRecCoord->getY();
        $Z = $geoEclRecCoord->getZ();

        $this->assertEquals(0.621745, round($X, 6));
        $this->assertEquals(-0.664812, round($Y, 6));
        $this->assertEquals(-0.033133, round($Z, 6));
    }
}
