<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalSphericalCoordinates;
use Andrmoel\AstronomyBundle\Location;
use PHPUnit\Framework\TestCase;

class GeocentricEclipticalSphericalCoordinatesTest extends TestCase
{
    /**
     * @test
     */
    public function getGeocentricEclipticalRectangularCoordinatesTest()
    {
        $lon = 313.082894;
        $lat = -2.084721;
        $r = 0.910845;

        $geoEclSphCoord = new GeocentricEclipticalSphericalCoordinates($lon, $lat, $r);
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
     */
    public function getGeocentricEquatorialRectangularCoordinatesTest()
    {
        $T = 0.021;
        $lon = 113.215630;
        $lat = 6.684170;
        $r = 0.910845;

        $geoEclSphCoord = new GeocentricEclipticalSphericalCoordinates($lon, $lat, $r);
        $geoEquRecCoord = $geoEclSphCoord->getGeocentricEquatorialRectangularCoordinates($T);

        $X = $geoEquRecCoord->getX();
        $Y = $geoEquRecCoord->getY();
        $Z = $geoEquRecCoord->getZ();

        // TODO Validate, if the values are correct
        $this->assertEquals(-0.356608, round($X, 6));
        $this->assertEquals(0.720625, round($Y, 6));
        $this->assertEquals(0.427983, round($Z, 6));
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

        $geoEclSphCoord = new GeocentricEclipticalSphericalCoordinates($lon, $lat);
        $geoEquSphCoord = $geoEclSphCoord->getGeocentricEquatorialSphericalCoordinates($T);

        $ra = $geoEquSphCoord->getRightAscension();
        $d = $geoEquSphCoord->getDeclination();

        $this->assertEquals(116.328943, round($ra, 6));
        $this->assertEquals(28.026183, round($d, 6));
    }

    /**
     * @test
     */
    public function getLocalHorizontalCoordinatesTest()
    {
        $location = new Location(38.921389, -77.065556);
        $T = -0.12727429842574; // 1987-04-10 19:21:00
        $lon = 345.72253406182;
        $lat = -1.1827089713027;

        $geoEclSphCoord = new GeocentricEclipticalSphericalCoordinates($lon, $lat);
        $locHorCoord = $geoEclSphCoord->getLocalHorizontalCoordinates($location, $T);

        $azimuth = $locHorCoord->getAzimuth();
        $altitude = $locHorCoord->getAltitude();

        $this->assertEquals(248.0325, round($azimuth, 4));
        $this->assertEquals(15.1247, round($altitude, 4));
    }
}
