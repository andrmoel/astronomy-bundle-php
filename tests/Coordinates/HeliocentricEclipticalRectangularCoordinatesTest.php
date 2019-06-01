<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Coordinates\HeliocentricEclipticalRectangularCoordinates;
use Andrmoel\AstronomyBundle\Location;
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
     */
    public function getHeliocentricEquatorialRectangularCoordinatesTest()
    {
        // TODO
    }

    /**
     * @test
     */
    public function getHeliocentricEquatorialSphericalCoordinatesTest()
    {
        // TODO
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

    /**
     * @test
     */
    public function getGeocentricEclipticalSphericalCoordinatesTest()
    {
        $T = -0.070321697467488;
        $x = 0.64995327095595;
        $y = 0.31860745636351;
        $z = -0.033130385747949;

        $helEclRecCoord = new HeliocentricEclipticalRectangularCoordinates($x, $y, $z);
        $geoEclSphCoord = $helEclRecCoord->getGeocentricEclipticalSphericalCoordinates($T);

        $lat = $geoEclSphCoord->getLatitude();
        $lon = $geoEclSphCoord->getLongitude();
        $radiusVector = $geoEclSphCoord->getRadiusVector();

        $this->assertEquals(-2.084671, round($lat, 6));
        $this->assertEquals(313.082766, round($lon, 6));
        $this->assertEquals(0.910846, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEquatorialRectangularCoordinatesTest()
    {
        $T = -0.070321697467488;
        $x = 0.64995327095595;
        $y = 0.31860745636351;
        $z = -0.033130385747949;

        $helEclRecCoord = new HeliocentricEclipticalRectangularCoordinates($x, $y, $z);
        $geoEquRecCoord = $helEclRecCoord->getGeocentricEquatorialRectangularCoordinates($T);

        $X = $geoEquRecCoord->getX();
        $Y = $geoEquRecCoord->getY();
        $Z = $geoEquRecCoord->getZ();

        $this->assertEquals(0.621745, round($X, 6));
        $this->assertEquals(-0.596772, round($Y, 6));
        $this->assertEquals(-0.29485, round($Z, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEquatorialSphericalCoordinatesTest()
    {
        $T = -0.070321697467488;
        $x = 0.64995327095595;
        $y = 0.31860745636351;
        $z = -0.033130385747949;

        $helEclRecCoord = new HeliocentricEclipticalRectangularCoordinates($x, $y, $z);
        $geoEquSphCoord = $helEclRecCoord->getGeocentricEquatorialSphericalCoordinates($T);

        $rightAscension = $geoEquSphCoord->getRightAscension();
        $declination = $geoEquSphCoord->getDeclination();
        $radiusVector = $geoEquSphCoord->getRadiusVector();

        $this->assertEquals(316.174117, round($rightAscension, 6));
        $this->assertEquals(-18.887456, round($declination, 6));
        $this->assertEquals(0.910846, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getLocalHorizontalCoordinatesTest()
    {
        $location = new Location(38.921389, -77.065556);
        $T = -0.070321697467488;
        $x = 0.64995327095595;
        $y = 0.31860745636351;
        $z = -0.033130385747949;

        $helEclRecCoord = new HeliocentricEclipticalRectangularCoordinates($x, $y, $z);
        $locHorCoord = $helEclRecCoord->getLocalHorizontalCoordinates($location, $T);

        $azimuth = $locHorCoord->getAzimuth();
        $altitude = $locHorCoord->getAltitude();

        $this->assertEquals(233.02118, round($azimuth, 5));
        $this->assertEquals(12.27574, round($altitude, 5));
    }
}
