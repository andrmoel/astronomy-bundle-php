<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalSphericalCoordinates;
use Andrmoel\AstronomyBundle\Location;
use PHPUnit\Framework\TestCase;

class GeocentricEclipticalSphericalCoordinatesTest extends TestCase
{
    private $T = -0.070321697467488; // 1992-12-20 00:00:00

    /** @var GeocentricEclipticalSphericalCoordinates */
    private $geoEclSphCoord;

    public function setUp()
    {
        $longitude = 314.23219;
        $latitude = -2.051607;
        $radiusVector = 0.903743;

        $this->geoEclSphCoord = new GeocentricEclipticalSphericalCoordinates($longitude, $latitude, $radiusVector);
    }

    /**
     * @test
     */
    public function getGeocentricEclipticalRectangularCoordinatesTest()
    {
        $geoEclRecCoord = $this->geoEclSphCoord->getGeocentricEclipticalRectangularCoordinates();

        $x = $geoEclRecCoord->getX();
        $y = $geoEclRecCoord->getY();
        $z = $geoEclRecCoord->getZ();

        $this->assertEquals(0.630018, round($x, 6));
        $this->assertEquals(-0.647134, round($y, 6));
        $this->assertEquals(-0.032354, round($z, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEquatorialRectangularCoordinatesTest()
    {
        $geoEquRecCoord = $this->geoEclSphCoord->getGeocentricEquatorialRectangularCoordinates($this->T);

        $x = $geoEquRecCoord->getX();
        $y = $geoEquRecCoord->getY();
        $z = $geoEquRecCoord->getZ();

        $this->assertEquals(0.630018, round($x, 6));
        $this->assertEquals(-0.580862, round($y, 6));
        $this->assertEquals(-0.287103, round($z, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEquatorialSphericalCoordinatesTest()
    {
        $geoEquSphCoord = $this->geoEclSphCoord->getGeocentricEquatorialSphericalCoordinates($this->T);

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
        $location = new Location(38.921389, -77.065556);

        $locHorCoord = $this->geoEclSphCoord->getLocalHorizontalCoordinates($location, $this->T);

        $azimuth = $locHorCoord->getAzimuth();
        $altitude = $locHorCoord->getAltitude();

        $this->assertEquals(232.41984, round($azimuth, 5));
        $this->assertEquals(13.26398, round($altitude, 5));
    }
}
