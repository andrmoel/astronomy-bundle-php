<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialSphericalCoordinates;
use Andrmoel\AstronomyBundle\Location;
use PHPUnit\Framework\TestCase;

class GeocentricEquatorialSphericalCoordinatesTest extends TestCase
{
    /**
     * @test
     */
    public function getGeocentricEclipticalRectangularCoordinatesTest()
    {
        $T = -0.070321697467488; // 1992-12-20 00:00:00
        $rightAscension = 316.174262;
        $declination = -18.887468;
        $radiusVector = 0.910845;

        $geoEquSphCoord = new GeocentricEquatorialSphericalCoordinates($rightAscension, $declination, $radiusVector);
        $geoEclRecCoord = $geoEquSphCoord->getGeocentricEclipticalRectangularCoordinates($T);

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
    public function getGeocentricEclipticalSphericalCoordinatesTest()
    {
        $T = 0.021;
        $ra = 116.328942;
        $d = 28.026183;

        $geoEquSphCoord = new GeocentricEquatorialSphericalCoordinates($ra, $d);
        $geoEclSphCoord = $geoEquSphCoord->getGeocentricEclipticalSphericalCoordinates($T);

        $lon = $geoEclSphCoord->getLongitude();
        $lat = $geoEclSphCoord->getLatitude();

        $this->assertEquals(113.215630, round($lon, 6));
        $this->assertEquals(6.684170, round($lat, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEquatorialRectangularCoordinatesTest()
    {
        $rightAscension = 316.174262;
        $declination = -18.887468;
        $radiusVector = 0.910845;

        $geoEquSphCoord = new GeocentricEquatorialSphericalCoordinates($rightAscension, $declination, $radiusVector);
        $geoEquRecCoord = $geoEquSphCoord->getGeocentricEquatorialRectangularCoordinates();

        $X = $geoEquRecCoord->getX();
        $Y = $geoEquRecCoord->getY();
        $Z = $geoEquRecCoord->getZ();

        $this->assertEquals(0.621746, round($X, 6));
        $this->assertEquals(-0.596769, round($Y, 6));
        $this->assertEquals(-0.29485, round($Z, 6));
    }

    /**
     * @test
     * Meeus 13.b
     */
    public function getLocalHorizontalCoordinatesTest()
    {
        $location = new Location(38.921389, -77.065556);
        $T = -0.12727429842574; // 1987-04-10 19:21:00
        $rightAscension = 347.3193375;
        $declination = -6.719891667;

        $geoEquSphCoord = new GeocentricEquatorialSphericalCoordinates($rightAscension, $declination);
        $locHorCoord = $geoEquSphCoord->getLocalHorizontalCoordinates($location, $T);

        $azimuth = $locHorCoord->getAzimuth();
        $altitude = $locHorCoord->getAltitude();

        $this->assertEquals(248.0336, round($azimuth, 4));
        $this->assertEquals(15.125, round($altitude, 4));
    }
}
