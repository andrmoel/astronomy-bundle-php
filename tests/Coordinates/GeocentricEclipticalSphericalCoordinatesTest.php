<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalSphericalCoordinates;
use PHPUnit\Framework\TestCase;

class GeocentricEclipticalSphericalCoordinatesTest extends TestCase
{
    /**
     * @test
     */
    public function getGeocentricEquatorialRectangularCoordinatesTest()
    {
        $lat = -2.084721;
        $lon = 313.082894;
        $r = 0.910845;

        $geoEclSphCoord = new GeocentricEclipticalSphericalCoordinates($lat, $lon, $r);
        $geoEclRecCoord = $geoEclSphCoord->getGeocentricEclipticalRectangularCoordinates();

        $X = $geoEclRecCoord->getX();
        $Y = $geoEclRecCoord->getY();
        $Z = $geoEclRecCoord->getZ();

        $this->assertEquals(0.621746, round($X, 6));
        $this->assertEquals(-0.664810, round($Y, 6));
        $this->assertEquals(-0.033134, round($Z, 6));
    }

    /**
     * @test
     * Meeus 13.a
     */
    public function getGeocentricEquatorialSphericalCoordinatesTest()
    {
        $T = 0.021;
        $lon = 113.215630;
        $lat = 6.684170;

        $geoEclSphCoord = new GeocentricEclipticalSphericalCoordinates($lat, $lon);
        $geoEquSphCoord = $geoEclSphCoord->getGeocentricEquatorialSphericalCoordinates($T);

        $ra = $geoEquSphCoord->getRightAscension();
        $d = $geoEquSphCoord->getDeclination();

        $this->assertEquals(116.328943, round($ra, 6));
        $this->assertEquals(28.026183, round($d, 6));
    }
}
