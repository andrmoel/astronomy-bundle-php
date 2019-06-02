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
        $lon = 26.11412;
        $lat = -2.620603;
        $radiusVector = 0.724602;

        $helEclSphCoord = new HeliocentricEclipticalSphericalCoordinates($lon, $lat, $radiusVector);
        $helEclRecCoord = $helEclSphCoord->getHeliocentricEclipticalRectangularCoordinates();

        $x = $helEclRecCoord->getX();
        $y = $helEclRecCoord->getY();
        $z = $helEclRecCoord->getZ();

        $this->assertEquals(0.649954, round($x, 6));
        $this->assertEquals(0.318608, round($y, 6));
        $this->assertEquals(-0.033130, round($z, 6));
    }

    /**
     * @test
     */
    public function getHeliocentricEquatorialRectangularCoordinatesTest()
    {
        $T = -0.070321697467488;
        $lon = 26.11412;
        $lat = -2.620603;
        $radiusVector = 0.724602;

        $helEclSphCoord = new HeliocentricEclipticalSphericalCoordinates($lon, $lat, $radiusVector);
        $helEquRecCoord = $helEclSphCoord->getHeliocentricEquatorialRectangularCoordinates($T);

        $X = $helEquRecCoord->getX();
        $Y = $helEquRecCoord->getY();
        $Z = $helEquRecCoord->getZ();

        $this->assertEquals(0.649954, round($X, 6));
        $this->assertEquals(0.305495, round($Y, 6));
        $this->assertEquals(0.09634, round($Z, 6));
    }

    /**
     * @test
     */
    public function getHeliocentricEquatorialSphericalCoordinatesTest()
    {
        $T = -0.070321697467488;
        $lon = 26.11412;
        $lat = -2.620603;
        $radiusVector = 0.724602;

        $helEclSphCoord = new HeliocentricEclipticalSphericalCoordinates($lon, $lat, $radiusVector);
        $helEquSphCoord = $helEclSphCoord->getHeliocentricEquatorialSphericalCoordinates($T);

        $rightAcension = $helEquSphCoord->getRightAscension();
        $declination = $helEquSphCoord->getDeclination();
        $radiusVector = $helEquSphCoord->getRadiusVector();

        $this->assertEquals(25.174714, round($rightAcension, 6));
        $this->assertEquals(7.64045, round($declination, 6));
        $this->assertEquals(0.724602, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEclipticalRectangularCoordinatesTest()
    {
        $T = -0.070321697467488;
        $lon = 26.11412;
        $lat = -2.620603;
        $radiusVector = 0.724602;

        $helEclSphCoord = new HeliocentricEclipticalSphericalCoordinates($lon, $lat, $radiusVector);
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
        $lon = 26.11412;
        $lat = -2.620603;
        $radiusVector = 0.724602;

        $helEclSphCoord = new HeliocentricEclipticalSphericalCoordinates($lon, $lat, $radiusVector);
        $geoEclSphCoord = $helEclSphCoord->getGeocentricEclipticalSphericalCoordinates($T);

        $lat = $geoEclSphCoord->getLatitude();
        $lon = $geoEclSphCoord->getLongitude();
        $radiusVector = $geoEclSphCoord->getRadiusVector();

        $this->assertEquals(313.082785, round($lon, 6));
        $this->assertEquals(-2.084671, round($lat, 6));
        $this->assertEquals(0.910846, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEquatorialRectangularCoordinatesTest()
    {
        $T = -0.070321697467488;
        $lon = 26.11412;
        $lat = -2.620603;
        $radiusVector = 0.724602;

        $helEclSphCoord = new HeliocentricEclipticalSphericalCoordinates($lon, $lat, $radiusVector);
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
        $lon = 26.11412;
        $lat = -2.620603;
        $radiusVector = 0.724602;

        $helEclSphCoord = new HeliocentricEclipticalSphericalCoordinates($lon, $lat, $radiusVector);
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

        $helEclSphCoord = new HeliocentricEclipticalSphericalCoordinates($lon, $lat, $radiusVector);
        $locHorCoord = $helEclSphCoord->getLocalHorizontalCoordinates($location, $T);

        $azimuth = $locHorCoord->getAzimuth();
        $altitude = $locHorCoord->getAltitude();

        $this->assertEquals(233.02117, round($azimuth, 5));
        $this->assertEquals(12.27575, round($altitude, 5));
    }
}
