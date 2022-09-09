<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialSphericalCoordinates;
use Andrmoel\AstronomyBundle\Location;
use PHPUnit\Framework\TestCase;

class GeocentricEquatorialSphericalCoordinatesTest extends TestCase
{
    private $T = -0.070321697467488; // 1992-12-20 00:00:00

    /** @var GeocentricEquatorialSphericalCoordinates */
    private $geoEquSphCoord;

    public function setUp(): void
    {
        $rightAscension = 316.175027;
        $declination = -18.887572;
        $radiusVector = 0.910841;

        $this->geoEquSphCoord = new GeocentricEquatorialSphericalCoordinates($rightAscension, $declination, $radiusVector);
    }

    /**
     * @test
     */
    public function getGeocentricEclipticalRectangularCoordinatesTest()
    {
        $geoEclRecCoord = $this->geoEquSphCoord->getGeocentricEclipticalRectangularCoordinates($this->T);

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
        $geoEclSphCoord = $this->geoEquSphCoord->getGeocentricEclipticalSphericalCoordinates($this->T);

        $longitude = $geoEclSphCoord->getLongitude();
        $latitude = $geoEclSphCoord->getLatitude();
        $radiusVector = $geoEclSphCoord->getRadiusVector();

        $this->assertEquals(313.083558, round($longitude, 6));
        $this->assertEquals(-2.085029, round($latitude, 6));
        $this->assertEquals(0.910841, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEquatorialRectangularCoordinatesTest()
    {
        $geoEquRecCoord = $this->geoEquSphCoord->getGeocentricEquatorialRectangularCoordinates();

        $x = $geoEquRecCoord->getX();
        $y = $geoEquRecCoord->getY();
        $z = $geoEquRecCoord->getZ();

        $this->assertEquals(0.621751, round($x, 6));
        $this->assertEquals(-0.596758, round($y, 6));
        $this->assertEquals(-0.29485, round($z, 6));
    }

    /**
     * @test
     * Meeus 13.b
     */
    public function getLocalHorizontalCoordinatesTest()
    {
        $location = Location::create(38.921389, -77.065556);

        $locHorCoord = $this->geoEquSphCoord->getLocalHorizontalCoordinates($location, $this->T);

        $azimuth = $locHorCoord->getAzimuth();
        $altitude = $locHorCoord->getAltitude();

        $this->assertEquals(233.02044, round($azimuth, 5));
        $this->assertEquals(12.27621, round($altitude, 5));
    }
}
