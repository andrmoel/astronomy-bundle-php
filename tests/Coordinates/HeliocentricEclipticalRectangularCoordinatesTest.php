<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Coordinates\HeliocentricEclipticalRectangularCoordinates;
use Andrmoel\AstronomyBundle\Location;
use PHPUnit\Framework\TestCase;

class HeliocentricEclipticalRectangularCoordinatesTest extends TestCase
{
    private $T = -0.070321697467488; // 1992-12-20 00:00:00

    /** @var HeliocentricEclipticalRectangularCoordinates */
    private $helEclRecCoord;

    public function setUp()
    {
        $x = 0.6499472;
        $y = 0.3186199;
        $z = -0.0331298;

        $this->helEclRecCoord = new HeliocentricEclipticalRectangularCoordinates($x, $y, $z);
    }

    /**
     * @test
     */
    public function getHeliocentricEclipticalSphericalCoordinatesTest()
    {
        $helEclSphCoord = $this->helEclRecCoord->getHeliocentricEclipticalSphericalCoordinates();

        $longitude = $helEclSphCoord->getLongitude();
        $latitude = $helEclSphCoord->getLatitude();
        $radiusVector = $helEclSphCoord->getRadiusVector();

        $this->assertEquals(26.115216, round($longitude, 6));
        $this->assertEquals(-2.620557, round($latitude, 6));
        $this->assertEquals(0.724602, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getHeliocentricEquatorialRectangularCoordinatesTest()
    {
        $helEquRecCoord = $this->helEclRecCoord->getHeliocentricEquatorialRectangularCoordinates($this->T);

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
        $helEquSphCoord = $this->helEclRecCoord->getHeliocentricEquatorialSphericalCoordinates($this->T);

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
        $geoEclRecCoord = $this->helEclRecCoord->getGeocentricEclipticalRectangularCoordinates($this->T);

        $x = $geoEclRecCoord->getX();
        $y = $geoEclRecCoord->getY();
        $z = $geoEclRecCoord->getZ();

        $this->assertEquals(0.621739, round($x, 6));
        $this->assertEquals(-0.6648, round($y, 6));
        $this->assertEquals(-0.033133, round($z, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEclipticalSphericalCoordinatesTest()
    {
        $geoEclSphCoord = $this->helEclRecCoord->getGeocentricEclipticalSphericalCoordinates($this->T);

        $longitude = $geoEclSphCoord->getLongitude();
        $latitude = $geoEclSphCoord->getLatitude();
        $radiusVector = $geoEclSphCoord->getRadiusVector();

        $this->assertEquals(313.083022, round($longitude, 6));
        $this->assertEquals(-2.084664, round($latitude, 6));
        $this->assertEquals(0.910833, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEquatorialRectangularCoordinatesTest()
    {
        $geoEquRecCoord = $this->helEclRecCoord->getGeocentricEquatorialRectangularCoordinates($this->T);

        $x = $geoEquRecCoord->getX();
        $y = $geoEquRecCoord->getY();
        $z = $geoEquRecCoord->getZ();

        $this->assertEquals(0.621739, round($x, 6));
        $this->assertEquals(-0.596761, round($y, 6));
        $this->assertEquals(-0.294845, round($z, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEquatorialSphericalCoordinatesTest()
    {
        $geoEquSphCoord = $this->helEclRecCoord->getGeocentricEquatorialSphericalCoordinates($this->T);

        $rightAscension = $geoEquSphCoord->getRightAscension();
        $declination = $geoEquSphCoord->getDeclination();
        $radiusVector = $geoEquSphCoord->getRadiusVector();

        $this->assertEquals(316.174374, round($rightAscension, 6));
        $this->assertEquals(-18.887377, round($declination, 6));
        $this->assertEquals(0.910833, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getLocalHorizontalCoordinatesTest()
    {
        $location = Location::create(38.921389, -77.065556);

        $locHorCoord = $this->helEclRecCoord->getLocalHorizontalCoordinates($location, $this->T);

        $azimuth = $locHorCoord->getAzimuth();
        $altitude = $locHorCoord->getAltitude();

        $this->assertEquals(233.02104, round($azimuth, 5));
        $this->assertEquals(12.27595, round($altitude, 5));
    }
}
