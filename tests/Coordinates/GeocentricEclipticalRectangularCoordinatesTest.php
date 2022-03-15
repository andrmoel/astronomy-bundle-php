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

    public function setUp(): void
    {
        $x = 0.6217509;
        $y = -0.6648001;
        $z = -0.0331326;

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

        $this->assertEquals(313.083545, round($longitude, 6));
        $this->assertEquals(-2.084642, round($latitude, 6));
        $this->assertEquals(0.910841, round($radiusVector, 6));
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

        $this->assertEquals(0.621751, round($X, 6));
        $this->assertEquals(-0.596761, round($Y, 6));
        $this->assertEquals(-0.294845, round($Z, 6));
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

        $this->assertEquals(316.174896, round($rightAscension, 6));
        $this->assertEquals(-18.887205, round($declination, 6));
        $this->assertEquals(0.910841, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getLocalHorizontalCoordinatesTest()
    {
        $location = Location::create(38.9213, -77.0655);

        $locHorCoord = $this->geoEclRecCoord->getLocalHorizontalCoordinates($location, $this->T);

        $azimuth = $locHorCoord->getAzimuth();
        $altitude = $locHorCoord->getAltitude();

        $this->assertEquals(233.02083, round($azimuth, 5));
        $this->assertEquals(12.27643, round($altitude, 5));
    }
}
