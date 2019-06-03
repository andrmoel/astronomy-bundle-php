<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Coordinates\HeliocentricEquatorialSphericalCoordinates;
use Andrmoel\AstronomyBundle\Location;
use PHPUnit\Framework\TestCase;

class HeliocentricEquatorialSphericalCoordinatesTest extends TestCase
{
    private $T = -0.070321697467488; // 1992-12-20 00:00:00

    /** @var HeliocentricEquatorialSphericalCoordinates */
    private $helEquSphCoord;

    public function setUp()
    {
        $rightAscension = 25.175663;
        $declination = 7.641117;
        $radiusVector = 0.724602;

        $this->helEquSphCoord = new HeliocentricEquatorialSphericalCoordinates($rightAscension, $declination, $radiusVector);
    }

    /**
     * @test
     */
    public function getHeliocentricEclipticalRectangularCoordinatesTest()
    {
        $helEclRecCoord = $this->helEquSphCoord->getHeliocentricEclipticalRectangularCoordinates($this->T);

        $x = $helEclRecCoord->getX();
        $y = $helEclRecCoord->getY();
        $z = $helEclRecCoord->getZ();

        $this->assertEquals(0.649947, round($x, 6));
        $this->assertEquals(0.31862, round($y, 6));
        $this->assertEquals(-0.033127, round($z, 6));
    }

    /**
     * @test
     */
    public function getHeliocentricEclipticalSphericalCoordinatesTest()
    {
        $helEclSphCoord = $this->helEquSphCoord->getHeliocentricEclipticalSphericalCoordinates($this->T);

        $longitude = $helEclSphCoord->getLongitude();
        $latitude = $helEclSphCoord->getLatitude();
        $radiusVector = $helEclSphCoord->getRadiusVector();

        $this->assertEquals(26.115239, round($longitude, 6));
        $this->assertEquals(-2.62032, round($latitude, 6));
        $this->assertEquals(0.724602, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getHeliocentricEquatorialRectangularCoordinatesTest()
    {
        $helEquRecCoord = $this->helEquSphCoord->getHeliocentricEquatorialRectangularCoordinates();

        $x = $helEquRecCoord->getX();
        $y = $helEquRecCoord->getY();
        $z = $helEquRecCoord->getZ();

        $this->assertEquals(0.649947, round($x, 6));
        $this->assertEquals(0.305505, round($y, 6));
        $this->assertEquals(0.096349, round($z, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEclipticalRectangularCoordinatesTest()
    {
        $geoEclRecCoord = $this->helEquSphCoord->getGeocentricEclipticalRectangularCoordinates($this->T);

        $x = $geoEclRecCoord->getX();
        $y = $geoEclRecCoord->getY();
        $z = $geoEclRecCoord->getZ();

        $this->assertEquals(0.62174, round($x, 6));
        $this->assertEquals(-0.664799, round($y, 6));
        $this->assertEquals(-0.03313, round($z, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEclipticalSphericalCoordinatesTest()
    {
        $geoEclSphCoord = $this->helEquSphCoord->getGeocentricEclipticalSphericalCoordinates($this->T);

        $longitude = $geoEclSphCoord->getLongitude();
        $latitude = $geoEclSphCoord->getLatitude();
        $radiusVector = $geoEclSphCoord->getRadiusVector();

        $this->assertEquals(313.083055, round($longitude, 6));
        $this->assertEquals(-2.084478, round($latitude, 6));
        $this->assertEquals(0.910832, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEquatorialRectangularCoordinatesTest()
    {
        $geoEquRecCoord = $this->helEquSphCoord->getGeocentricEquatorialRectangularCoordinates($this->T);

        $x = $geoEquRecCoord->getX();
        $y = $geoEquRecCoord->getY();
        $z = $geoEquRecCoord->getZ();

        $this->assertEquals(0.62174, round($x, 6));
        $this->assertEquals(-0.596761, round($y, 6));
        $this->assertEquals(-0.294842, round($z, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEquatorialSphericalCoordinatesTest()
    {
        $geoEquSphCoord = $this->helEquSphCoord->getGeocentricEquatorialSphericalCoordinates($this->T);

        $rightAscension = $geoEquSphCoord->getRightAscension();
        $declination = $geoEquSphCoord->getDeclination();
        $radiusVector = $geoEquSphCoord->getRadiusVector();

        $this->assertEquals(316.174351, round($rightAscension, 6));
        $this->assertEquals(-18.887189, round($declination, 6));
        $this->assertEquals(0.910832, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getLocalHorizontalCoordinatesTest()
    {
        $location = new Location(38.921389, -77.065556);

        $locHorCoord = $this->helEquSphCoord->getLocalHorizontalCoordinates($location, $this->T);

        $azimuth = $locHorCoord->getAzimuth();
        $altitude = $locHorCoord->getAltitude();

        $this->assertEquals(233.02119, round($azimuth, 5));
        $this->assertEquals(12.27608, round($altitude, 5));
    }
}
