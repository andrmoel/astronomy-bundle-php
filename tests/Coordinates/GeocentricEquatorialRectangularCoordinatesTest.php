<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialRectangularCoordinates;
use Andrmoel\AstronomyBundle\Location;
use PHPUnit\Framework\TestCase;

class GeocentricEquatorialRectangularCoordinatesTest extends TestCase
{
    /**
     * @test
     */
    public function getGeocentricEclipticalRectangularCoordinatesTest()
    {
        $T = -0.070321697467488; // 1992-12-20 00:00:00
        $X = 0.621746;
        $Y = -0.596769;
        $Z = -0.29485;

        $geoEquRecCoord = new GeocentricEquatorialRectangularCoordinates($X, $Y, $Z);
        $geoEclRecCoord = $geoEquRecCoord->getGeocentricEclipticalRectangularCoordinates($T);

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
    public function getGeocentricEclipticalSphericalCoordinatesTest()
    {
        $T = -0.070321697467488; // 1992-12-20 00:00:00
        $X = 0.621746;
        $Y = -0.596769;
        $Z = -0.29485;

        $geoEquRecCoord = new GeocentricEquatorialRectangularCoordinates($X, $Y, $Z);
        $geoEclSphCoord = $geoEquRecCoord->getGeocentricEclipticalSphericalCoordinates($T);

        $lat = $geoEclSphCoord->getLatitude();
        $lon = $geoEclSphCoord->getLongitude();
        $radiusVector = $geoEclSphCoord->getRadiusVector();

        $this->assertEquals(-2.08473, round($lat, 6));
        $this->assertEquals(313.082908, round($lon, 6));
        $this->assertEquals(0.910845, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEquatorialSphericalCoordinatesTest()
    {
        $X = 0.621746;
        $Y = -0.596769;
        $Z = -0.29485;

        $geoEquRecCoord = new GeocentricEquatorialRectangularCoordinates($X, $Y, $Z);
        $geoEquSphCoord = $geoEquRecCoord->getGeocentricEquatorialSphericalCoordinates();

        $rightAscension = $geoEquSphCoord->getRightAscension();
        $declination = $geoEquSphCoord->getDeclination();
        $radiusVector = $geoEquSphCoord->getRadiusVector();

        $this->assertEquals(316.174279, round($rightAscension, 6));
        $this->assertEquals(-18.887472, round($declination, 6));
        $this->assertEquals(0.910845, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getLocalHorizontalCoordinatesTest()
    {
        $location = new Location(38.921389, -77.065556);
        $T = -0.070321697467488; // 1992-12-20 00:00:00
        $X = 0.621746;
        $Y = -0.596769;
        $Z = -0.29485;

        $geoEquRecCoord = new GeocentricEquatorialRectangularCoordinates($X, $Y, $Z);
        $locHorCoord = $geoEquRecCoord->getLocalHorizontalCoordinates($location, $T);

        $azimuth = $locHorCoord->getAzimuth();
        $altitude = $locHorCoord->getAltitude();

        $this->assertEquals(233.02105, round($azimuth, 5));
        $this->assertEquals(12.27582, round($altitude, 5));
    }
}
