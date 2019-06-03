<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialSphericalCoordinates;
use Andrmoel\AstronomyBundle\Location;
use PHPUnit\Framework\TestCase;

class GeocentricEquatorialSphericalCoordinatesTest extends TestCase
{
    private $T = -0.070321697467488; // 1992-12-20 00:00:00

    /** @var GeocentricEquatorialSphericalCoordinates */
    private $geoEquSphCoord;

    public function setUp()
    {
        $rightAscension = 317.324778;
        $declination = -18.523153;
        $radiusVector = 0.903743;

        $this->geoEquSphCoord = new GeocentricEquatorialSphericalCoordinates($rightAscension, $declination, $radiusVector);
    }

    /**
     * @test
     */
    public function getGeocentricEclipticalRectangularCoordinatesTest()
    {
        $geoEclRecCoord = $this->geoEquSphCoord->getGeocentricEclipticalRectangularCoordinates($this->T);

        $x = $geoEclRecCoord->getX();
        $y = $geoEclRecCoord->getY();
        $z = $geoEclRecCoord->getZ();

        $this->assertEquals(0.630018, round($x, 6));
        $this->assertEquals(-0.647133, round($y, 6));
        $this->assertEquals(-0.03236, round($z, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEclipticalSphericalCoordinatesTest()
    {
        $geoEclSphCoord = $this->geoEquSphCoord->getGeocentricEclipticalSphericalCoordinates($this->T);

        $longitude = $geoEclSphCoord->getLongitude();
        $latitude = $geoEclSphCoord->getLatitude();
        $radiusVector = $geoEclSphCoord->getRadiusVector();

        $this->assertEquals(314.232204, round($longitude, 6));
        $this->assertEquals(-2.051988, round($latitude, 6));
        $this->assertEquals(0.903743, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEquatorialRectangularCoordinatesTest()
    {
        $geoEquRecCoord = $this->geoEquSphCoord->getGeocentricEquatorialRectangularCoordinates();

        $x = $geoEquRecCoord->getX();
        $y = $geoEquRecCoord->getY();
        $z = $geoEquRecCoord->getZ();

        $this->assertEquals(0.630018, round($x, 6));
        $this->assertEquals(-0.58086, round($y, 6));
        $this->assertEquals(-0.287108, round($z, 6));
    }

    /**
     * @test
     * Meeus 13.b
     */
    public function getLocalHorizontalCoordinatesTest()
    {
        $location = new Location(38.921389, -77.065556);

        $locHorCoord = $this->geoEquSphCoord->getLocalHorizontalCoordinates($location, $this->T);

        $azimuth = $locHorCoord->getAzimuth();
        $altitude = $locHorCoord->getAltitude();

        $this->assertEquals(232.41951, round($azimuth, 5));
        $this->assertEquals(13.26379, round($altitude, 5));
    }
}
