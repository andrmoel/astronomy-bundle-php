<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalSphericalCoordinates;
use PHPUnit\Framework\TestCase;

class GeocentricEclipticalSphericalCoordinatesTest extends TestCase
{
    /**
     * @test
     * Meeus 13.a
     */
    public function getGeocentricEclipticalSphericalCoordinatesTest()
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
