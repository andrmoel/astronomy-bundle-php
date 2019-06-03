<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Coordinates\HeliocentricEclipticalSphericalCoordinates;
use Andrmoel\AstronomyBundle\Location;
use PHPUnit\Framework\TestCase;

class HeliocentricEclipticalSphericalCoordinatesTest extends TestCase
{
    private $T = -0.070321697467488; // 1992-12-20 00:00:00

    /** @var HeliocentricEclipticalSphericalCoordinates */
    private $helEclSphCoord;

    public function setUp()
    {
        $longitude = 26.115216;
        $latitude = -2.620557;
        $radiusVector = 0.724602;

        $this->helEclSphCoord = new HeliocentricEclipticalSphericalCoordinates($longitude, $latitude, $radiusVector);
    }

    /**
     * @test
     */
    public function getHeliocentricEclipticalRectangularCoordinatesTest()
    {
        $helEclRecCoord = $this->helEclSphCoord->getHeliocentricEclipticalRectangularCoordinates();

        $x = $helEclRecCoord->getX();
        $y = $helEclRecCoord->getY();
        $z = $helEclRecCoord->getZ();

        $this->assertEquals(0.649947, round($x, 6));
        $this->assertEquals(0.31862, round($y, 6));
        $this->assertEquals(-0.033130, round($z, 6));
    }

    /**
     * @test
     */
    public function getHeliocentricEquatorialRectangularCoordinatesTest()
    {
        $helEquRecCoord = $this->helEclSphCoord->getHeliocentricEquatorialRectangularCoordinates($this->T);

        $x = $helEquRecCoord->getX();
        $y = $helEquRecCoord->getY();
        $z = $helEquRecCoord->getZ();

        $this->assertEquals(0.649947, round($x, 6));
        $this->assertEquals(0.305506, round($y, 6));
        $this->assertEquals(0.096346, round($z, 6));
    }

    /**
     * @test
     */
    public function getHeliocentricEquatorialSphericalCoordinatesTest()
    {
        $helEquSphCoord = $this->helEclSphCoord->getHeliocentricEquatorialSphericalCoordinates($this->T);

        $rightAscension = $helEquSphCoord->getRightAscension();
        $declination = $helEquSphCoord->getDeclination();
        $radiusVector = $helEquSphCoord->getRadiusVector();

        $this->assertEquals(25.175727, round($rightAscension, 6));
        $this->assertEquals(7.640888, round($declination, 6));
        $this->assertEquals(0.724602, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEclipticalRectangularCoordinatesTest()
    {
        $geoEclRecCoord = $this->helEclSphCoord->getGeocentricEclipticalRectangularCoordinates($this->T);

        $x = $geoEclRecCoord->getX();
        $y = $geoEclRecCoord->getY();
        $z = $geoEclRecCoord->getZ();

        $this->assertEquals(0.62174, round($x, 6));
        $this->assertEquals(-0.6648, round($y, 6));
        $this->assertEquals(-0.033133, round($z, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEclipticalSphericalCoordinatesTest()
    {
        $geoEclSphCoord = $this->helEclSphCoord->getGeocentricEclipticalSphericalCoordinates($this->T);

        $longitude = $geoEclSphCoord->getLongitude();
        $latitude = $geoEclSphCoord->getLatitude();
        $radiusVector = $geoEclSphCoord->getRadiusVector();

        $this->assertEquals(313.083041, round($longitude, 6));
        $this->assertEquals(-2.084665, round($latitude, 6));
        $this->assertEquals(0.910833, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEquatorialRectangularCoordinatesTest()
    {
        $geoEquRecCoord = $this->helEclSphCoord->getGeocentricEquatorialRectangularCoordinates($this->T);

        $x = $geoEquRecCoord->getX();
        $y = $geoEquRecCoord->getY();
        $z = $geoEquRecCoord->getZ();

        $this->assertEquals(0.62174, round($x, 6));
        $this->assertEquals(-0.59676, round($y, 6));
        $this->assertEquals(-0.294845, round($z, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEquatorialSphericalCoordinatesTest()
    {
        $geoEquSphCoord = $this->helEclSphCoord->getGeocentricEquatorialSphericalCoordinates($this->T);

        $rightAscension = $geoEquSphCoord->getRightAscension();
        $declination = $geoEquSphCoord->getDeclination();
        $radiusVector = $geoEquSphCoord->getRadiusVector();

        $this->assertEquals(316.174394, round($rightAscension, 6));
        $this->assertEquals(-18.887372, round($declination, 6));
        $this->assertEquals(0.910833, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getLocalHorizontalCoordinatesTest()
    {
        $location = new Location(38.921389, -77.065556);

        $locHorCoord = $this->helEclSphCoord->getLocalHorizontalCoordinates($location, $this->T);

        $azimuth = $locHorCoord->getAzimuth();
        $altitude = $locHorCoord->getAltitude();

        $this->assertEquals(233.02103, round($azimuth, 5));
        $this->assertEquals(12.27597, round($altitude, 5));
    }
}
