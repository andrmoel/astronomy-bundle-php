<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalRectangularCoordinates;
use Andrmoel\AstronomyBundle\Location;
use PHPUnit\Framework\TestCase;

class GeocentricEclipticalRectangularCoordinatesTest extends TestCase
{
    private $T = -0.070321697467488; // 1992-12-20 00:00:00

    /** @var GeocentricEclipticalRectangularCoordinates */
    private $geoEclRecCoord;

    public function setUp()
    {
        $x = 0.6300182;
        $y = -0.6471341;
        $z = -0.0323537;

        $this->geoEclRecCoord = new GeocentricEclipticalRectangularCoordinates($x, $y, $z);
    }

    /**
     * @test
     */
    public function getGeocentricEclipticalSphericalCoordinatesTest()
    {
        $geoEclSphCoord = $this->geoEclRecCoord->getGeocentricEclipticalSphericalCoordinates();

        $longitude = $geoEclSphCoord->getLongitude();
        $latitude = $geoEclSphCoord->getLatitude();
        $radiusVector = $geoEclSphCoord->getRadiusVector();

        $this->assertEquals(314.23219, round($longitude, 6));
        $this->assertEquals(-2.051607, round($latitude, 6));
        $this->assertEquals(0.903743, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEquatorialRectangularCoordinatesTest()
    {
        $geoEquRecCoord = $this->geoEclRecCoord->getGeocentricEquatorialRectangularCoordinates($this->T);

        $X = $geoEquRecCoord->getX();
        $Y = $geoEquRecCoord->getY();
        $Z = $geoEquRecCoord->getZ();

        $this->assertEquals(0.630018, round($X, 6));
        $this->assertEquals(-0.580862, round($Y, 6));
        $this->assertEquals(-0.287103, round($Z, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEquatorialSphericalCoordinatesTest()
    {
        $geoEquSphCoord = $this->geoEclRecCoord->getGeocentricEquatorialSphericalCoordinates($this->T);

        $rightAscension = $geoEquSphCoord->getRightAscension();
        $declination = $geoEquSphCoord->getDeclination();
        $radiusVector = $geoEquSphCoord->getRadiusVector();

        $this->assertEquals(317.324647, round($rightAscension, 6));
        $this->assertEquals(-18.522793, round($declination, 6));
        $this->assertEquals(0.903743, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getLocalHorizontalCoordinatesTest()
    {
        $location = new Location(38.9213, -77.0655);

        $locHorCoord = $this->geoEclRecCoord->getLocalHorizontalCoordinates($location, $this->T);

        $azimuth = $locHorCoord->getAzimuth();
        $altitude = $locHorCoord->getAltitude();

        $this->assertEquals(232.41984, round($azimuth, 5));
        $this->assertEquals(13.26398, round($altitude, 5));
    }
}
