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

    public function setUp(): void
    {
        $longitude = 313.083545;
        $latitude = -2.084642;
        $radiusVector = 0.910841;

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

        $this->assertEquals(0.621751, round($x, 6));
        $this->assertEquals(-0.6648, round($y, 6));
        $this->assertEquals(-0.033133, round($z, 6));
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

        $this->assertEquals(0.621751, round($x, 6));
        $this->assertEquals(-0.596761, round($y, 6));
        $this->assertEquals(-0.294845, round($z, 6));
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

        $this->assertEquals(316.174897, round($rightAscension, 6));
        $this->assertEquals(-18.887205, round($declination, 6));
        $this->assertEquals(0.910841, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getLocalHorizontalCoordinatesTest()
    {
        $location = Location::create(38.921389, -77.065556);

        $locHorCoord = $this->geoEclSphCoord->getLocalHorizontalCoordinates($location, $this->T);

        $azimuth = $locHorCoord->getAzimuth();
        $altitude = $locHorCoord->getAltitude();

        $this->assertEquals(233.02078, round($azimuth, 5));
        $this->assertEquals(12.27641, round($altitude, 5));
    }
}
