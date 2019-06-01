<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Coordinates\HeliocentricEclipticalSphericalCoordinates;
use Andrmoel\AstronomyBundle\Location;
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
        $radiusVector = 0.724602;

        $helEclSphCoord = new HeliocentricEclipticalSphericalCoordinates($lat, $lon, $radiusVector);
        $helEclRecCoord = $helEclSphCoord->getHeliocentricEclipticalRectangularCoordinates();

        $x = $helEclRecCoord->getX();
        $y = $helEclRecCoord->getY();
        $z = $helEclRecCoord->getZ();

        $this->assertEquals(0.649954, round($x, 6));
        $this->assertEquals(0.318608, round($y, 6));
        $this->assertEquals(-0.033130, round($z, 6));
    }

    public function getHeliocentricEquatorialRectangularCoordinatesTest()
    {
        // TODO
    }

    /**
     * @test
     */
    public function getGeocentricEclipticalRectangularCoordinatesTest()
    {
        $T = -0.070321697467488;
        $lat = -2.620603;
        $lon = 26.11412;
        $radiusVector = 0.724602;

        $helEclSphCoord = new HeliocentricEclipticalSphericalCoordinates($lat, $lon, $radiusVector);
        $geoEclRecCoord = $helEclSphCoord->getGeocentricEclipticalRectangularCoordinates($T);

        $X = $geoEclRecCoord->getX();
        $Y = $geoEclRecCoord->getY();
        $Z = $geoEclRecCoord->getZ();

        $this->assertEquals(0.621746, round($X, 6));
        $this->assertEquals(-0.664812, round($Y, 6));
        $this->assertEquals(-0.033133, round($Z, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEclipticalSphericalCoordinatesTest()
    {
        $T = -0.070321697467488;
        $lat = -2.620603;
        $lon = 26.11412;
        $radiusVector = 0.724602;

        $helEclSphCoord = new HeliocentricEclipticalSphericalCoordinates($lat, $lon, $radiusVector);
        $geoEclSphCoord = $helEclSphCoord->getGeocentricEclipticalSphericalCoordinates($T);

        $lat = $geoEclSphCoord->getLatitude();
        $lon = $geoEclSphCoord->getLongitude();
        $radiusVector = $geoEclSphCoord->getRadiusVector();

        $this->assertEquals(-2.084671, round($lat, 6));
        $this->assertEquals(313.082785, round($lon, 6));
        $this->assertEquals(0.910846, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEquatorialRectangularCoordinatesTest()
    {
        $T = -0.070321697467488;
        $lat = -2.620603;
        $lon = 26.11412;
        $radiusVector = 0.724602;

        $helEclSphCoord = new HeliocentricEclipticalSphericalCoordinates($lat, $lon, $radiusVector);
        $geoEquRecCoord = $helEclSphCoord->getGeocentricEquatorialRectangularCoordinates($T);

        $X = $geoEquRecCoord->getX();
        $Y = $geoEquRecCoord->getY();
        $Z = $geoEquRecCoord->getZ();

        $this->assertEquals(0.621746, round($X, 6));
        $this->assertEquals(-0.596772, round($Y, 6));
        $this->assertEquals(-0.29485, round($Z, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEquatorialSphericalCoordinatesTest()
    {
        $T = -0.070321697467488;
        $lat = -2.620603;
        $lon = 26.11412;
        $radiusVector = 0.724602;

        $helEclSphCoord = new HeliocentricEclipticalSphericalCoordinates($lat, $lon, $radiusVector);
        $geoEquSphCoord = $helEclSphCoord->getGeocentricEquatorialSphericalCoordinates($T);

        $rightAscension = $geoEquSphCoord->getRightAscension();
        $declination = $geoEquSphCoord->getDeclination();
        $radiusVector = $geoEquSphCoord->getRadiusVector();

        $this->assertEquals(316.174137, round($rightAscension, 6));
        $this->assertEquals(-18.887452, round($declination, 6));
        $this->assertEquals(0.910846, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getLocalHorizontalCoordinatesTest()
    {
        $location = new Location(38.921389, -77.065556);
        $T = -0.070321697467488;
        $lat = -2.620603;
        $lon = 26.11412;
        $radiusVector = 0.724602;

        $helEclSphCoord = new HeliocentricEclipticalSphericalCoordinates($lat, $lon, $radiusVector);
        $locHorCoord = $helEclSphCoord->getLocalHorizontalCoordinates($location, $T);

        $azimuth = $locHorCoord->getAzimuth();
        $altitude = $locHorCoord->getAltitude();

        $this->assertEquals(233.02117, round($azimuth, 5));
        $this->assertEquals(12.27575, round($altitude, 5));
    }
}
