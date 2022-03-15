<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Coordinates\HeliocentricEquatorialRectangularCoordinates;
use Andrmoel\AstronomyBundle\Location;
use PHPUnit\Framework\TestCase;

class HeliocentricEquatorialRectangularCoordinatesTest extends TestCase
{
    private $T = -0.070321697467488; // 1992-12-20 00:00:00

    /** @var HeliocentricEquatorialRectangularCoordinates */
    private $helEquRecCoord;

    public function setUp(): void
    {
        $x = 0.6499472;
        $y = 0.3055048;
        $z = 0.0963486;

        $this->helEquRecCoord = new HeliocentricEquatorialRectangularCoordinates($x, $y, $z);
    }

    /**
     * @test
     */
    public function getHeliocentricEclipticalRectangularCoordinatesTest()
    {
        $helEclRecCoord = $this->helEquRecCoord->getHeliocentricEclipticalRectangularCoordinates($this->T);

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
        $helEclSphCoord = $this->helEquRecCoord->getHeliocentricEclipticalSphericalCoordinates($this->T);

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
    public function getHeliocentricEquatorialSphericalCoordinatesTest()
    {
        $helEquSphCoord = $this->helEquRecCoord->getHeliocentricEquatorialSphericalCoordinates();

        $rightAscension = $helEquSphCoord->getRightAscension();
        $declination = $helEquSphCoord->getDeclination();
        $radiusVector = $helEquSphCoord->getRadiusVector();

        $this->assertEquals(25.175663, round($rightAscension, 6));
        $this->assertEquals(7.641117, round($declination, 6));
        $this->assertEquals(0.724602, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEclipticalRectangularCoordinatesTest()
    {
        $geoEclRecCoord = $this->helEquRecCoord->getGeocentricEclipticalRectangularCoordinates($this->T);

        $x = $geoEclRecCoord->getX();
        $y = $geoEclRecCoord->getY();
        $z = $geoEclRecCoord->getZ();

        $this->assertEquals(0.621739, round($x, 6));
        $this->assertEquals(-0.664799, round($y, 6));
        $this->assertEquals(-0.03313, round($z, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEclipticalSphericalCoordinatesTest()
    {
        $geoEclSphCoord = $this->helEquRecCoord->getGeocentricEclipticalSphericalCoordinates($this->T);

        $longitude = $geoEclSphCoord->getLongitude();
        $latitude = $geoEclSphCoord->getLatitude();
        $radiusVector = $geoEclSphCoord->getRadiusVector();

        $this->assertEquals(313.083036, round($longitude, 6));
        $this->assertEquals(-2.084477, round($latitude, 6));
        $this->assertEquals(0.910832, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEquatorialRectangularCoordinatesTest()
    {
        $geoEquRecCoord = $this->helEquRecCoord->getGeocentricEquatorialRectangularCoordinates($this->T);

        $x = $geoEquRecCoord->getX();
        $y = $geoEquRecCoord->getY();
        $z = $geoEquRecCoord->getZ();

        $this->assertEquals(0.621739, round($x, 6));
        $this->assertEquals(-0.596761, round($y, 6));
        $this->assertEquals(-0.294842, round($z, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEquatorialSphericalCoordinatesTest()
    {
        $geoEquSphCoord = $this->helEquRecCoord->getGeocentricEquatorialSphericalCoordinates($this->T);

        $rightAscension = $geoEquSphCoord->getRightAscension();
        $declination = $geoEquSphCoord->getDeclination();
        $radiusVector = $geoEquSphCoord->getRadiusVector();

        $this->assertEquals(316.174331, round($rightAscension, 6));
        $this->assertEquals(-18.887194, round($declination, 6));
        $this->assertEquals(0.910832, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getLocalHorizontalCoordinatesTest()
    {
        $location = Location::create(38.921389, -77.065556);

        $locHorCoord = $this->helEquRecCoord->getLocalHorizontalCoordinates($location, $this->T);

        $azimuth = $locHorCoord->getAzimuth();
        $altitude = $locHorCoord->getAltitude();

        $this->assertEquals(233.0212, round($azimuth, 5));
        $this->assertEquals(12.27607, round($altitude, 5));
    }
}
