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

    public function setUp(): void
    {
        $x = 0.6217509;
        $y = -0.5967581;
        $z = -0.2948503;

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

        $this->assertEquals(0.621751, round($x, 6));
        $this->assertEquals(-0.6648, round($y, 6));
        $this->assertEquals(-0.033139, round($z, 6));
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

        $this->assertEquals(313.083558, round($longitude, 6));
        $this->assertEquals(-2.085028, round($latitude, 6));
        $this->assertEquals(0.910841, round($radiusVector, 6));
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

        $this->assertEquals(316.175027, round($rightAscension, 6));
        $this->assertEquals(-18.887572, round($declination, 6));
        $this->assertEquals(0.910841, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getLocalHorizontalCoordinatesTest()
    {
        $location = Location::create(38.921389, -77.065556);

        $locHorCoord = $this->geoEquRecCoord->getLocalHorizontalCoordinates($location, $this->T);

        $azimuth = $locHorCoord->getAzimuth();
        $altitude = $locHorCoord->getAltitude();

        $this->assertEquals(233.02044, round($azimuth, 5));
        $this->assertEquals(12.27621, round($altitude, 5));
    }
}
