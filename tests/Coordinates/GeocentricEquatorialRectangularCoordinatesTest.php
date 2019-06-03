<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialRectangularCoordinates;
use Andrmoel\AstronomyBundle\Location;
use PHPUnit\Framework\TestCase;

class GeocentricEquatorialRectangularCoordinatesTest extends TestCase
{
    private $T = -0.070321697467488; // 1992-12-20 00:00:00

    /** @var GeocentricEquatorialRectangularCoordinates */
    private $geoEquRecCoord;

    public function setUp()
    {
        $x = 0.6300182;
        $y = -0.5808598;
        $z = -0.2871083;

        $this->geoEquRecCoord = new GeocentricEquatorialRectangularCoordinates($x, $y, $z);
    }

    /**
     * @test
     */
    public function getGeocentricEclipticalRectangularCoordinatesTest()
    {
        $geoEclRecCoord = $this->geoEquRecCoord->getGeocentricEclipticalRectangularCoordinates($this->T);

        $x = $geoEclRecCoord->getX();
        $y = $geoEclRecCoord->getY();
        $z = $geoEclRecCoord->getZ();

        $this->assertEquals(0.630018, round($x, 6));
        $this->assertEquals(-0.647134, round($y, 6));
        $this->assertEquals(-0.03236, round($z, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEclipticalSphericalCoordinatesTest()
    {
        $geoEclSphCoord = $this->geoEquRecCoord->getGeocentricEclipticalSphericalCoordinates($this->T);

        $longitude = $geoEclSphCoord->getLongitude();
        $latitude = $geoEclSphCoord->getLatitude();
        $radiusVector = $geoEclSphCoord->getRadiusVector();

        $this->assertEquals(314.232204, round($longitude, 6));
        $this->assertEquals(-2.051987, round($latitude, 6));
        $this->assertEquals(0.903743, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEquatorialSphericalCoordinatesTest()
    {
        $geoEquSphCoord = $this->geoEquRecCoord->getGeocentricEquatorialSphericalCoordinates();

        $rightAscension = $geoEquSphCoord->getRightAscension();
        $declination = $geoEquSphCoord->getDeclination();
        $radiusVector = $geoEquSphCoord->getRadiusVector();

        $this->assertEquals(317.324778, round($rightAscension, 6));
        $this->assertEquals(-18.523153, round($declination, 6));
        $this->assertEquals(0.903743, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getLocalHorizontalCoordinatesTest()
    {
        $location = new Location(38.921389, -77.065556);

        $locHorCoord = $this->geoEquRecCoord->getLocalHorizontalCoordinates($location, $this->T);

        $azimuth = $locHorCoord->getAzimuth();
        $altitude = $locHorCoord->getAltitude();

        $this->assertEquals(232.41951, round($azimuth, 5));
        $this->assertEquals(13.26379, round($altitude, 5));
    }
}
