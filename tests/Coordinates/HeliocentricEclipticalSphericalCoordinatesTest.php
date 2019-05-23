<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Coordinates\HeliocentricEclipticalSphericalCoordinates;
use PHPUnit\Framework\TestCase;

class HeliocentricEclipticalSphericalCoordinatesTest extends TestCase
{
    /**
     * @test
     */
    public function getHeliocentricEclipticalRectangularCoordinatesTest()
    {
        $lat = -2.620603;
        $lon = 26.11412;
        $r = 0.724602;

        $helEclSphCoord = new HeliocentricEclipticalSphericalCoordinates($lat, $lon, $r);
        $helEclRecCoord = $helEclSphCoord->getHeliocentricEclipticalRectangularCoordinates();

        $x = $helEclRecCoord->getX();
        $y = $helEclRecCoord->getY();
        $z = $helEclRecCoord->getZ();

        $this->assertEquals(0.649954, round($x, 6));
        $this->assertEquals(0.318608, round($y, 6));
        $this->assertEquals(-0.033130, round($z, 6));
    }
}
