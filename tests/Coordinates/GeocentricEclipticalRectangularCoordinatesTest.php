<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalRectangularCoordinates;
use Andrmoel\AstronomyBundle\Location;
use PHPUnit\Framework\TestCase;

class GeocentricEclipticalRectangularCoordinatesTest extends TestCase
{
    /**
     * @test
     */
    public function getGeocentricEclipticalSphericalCoordinatesTest()
    {
        $X = 0.621746;
        $Y = -0.664810;
        $Z = -0.033134;

        $geoEclRecCoord = new GeocentricEclipticalRectangularCoordinates($X, $Y, $Z);
        $geoEclSphCoord = $geoEclRecCoord->getGeocentricEclipticalSphericalCoordinates();

        $lat = $geoEclSphCoord->getLatitude();
        $lon = $geoEclSphCoord->getLongitude();
        $radiusVector = $geoEclSphCoord->getRadiusVector();

        $this->assertEquals(-2.084721, round($lat, 6));
        $this->assertEquals(313.082894, round($lon, 6));
        $this->assertEquals(0.910845, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEquatorialRectangularCoordinatesTest()
    {
        $T = -0.070321697467488; // 1992-12-20 00:00:00
        $X = 0.621746;
        $Y = -0.664810;
        $Z = -0.033134;

        $geoEclRecCoord = new GeocentricEclipticalRectangularCoordinates($X, $Y, $Z);
        $geoEquRecCoord = $geoEclRecCoord->getGeocentricEquatorialRectangularCoordinates($T);

        $X = $geoEquRecCoord->getX();
        $Y = $geoEquRecCoord->getY();
        $Z = $geoEquRecCoord->getZ();

        $this->assertEquals(0.621746, round($X, 6));
        $this->assertEquals(-0.596769, round($Y, 6));
        $this->assertEquals(-0.29485, round($Z, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEquatorialSphericalCoordinatesTest()
    {
        $T = -0.070321697467488; // 1992-12-20 00:00:00
        $X = 0.621746;
        $Y = -0.664810;
        $Z = -0.033134;

        $geoEclRecCoord = new GeocentricEclipticalRectangularCoordinates($X, $Y, $Z);
        $geoEquSphCoord = $geoEclRecCoord->getGeocentricEquatorialSphericalCoordinates($T);

        $rightAscension = $geoEquSphCoord->getRightAscension();
        $declination = $geoEquSphCoord->getDeclination();
        $radiusVector = $geoEquSphCoord->getRadiusVector();

        $this->assertEquals(316.174262, round($rightAscension, 6));
        $this->assertEquals(-18.887468, round($declination, 6));
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
        $Y = -0.664810;
        $Z = -0.033134;

        $geoEclRecCoord = new GeocentricEclipticalRectangularCoordinates($X, $Y, $Z);
        $locHorCoord = $geoEclRecCoord->getLocalHorizontalCoordinates($location, $T);

        $azimuth = $locHorCoord->getAzimuth();
        $altitude = $locHorCoord->getAltitude();

        $this->assertEquals(233.02106, round($azimuth, 5));
        $this->assertEquals(12.27582, round($altitude, 5));
    }
}
