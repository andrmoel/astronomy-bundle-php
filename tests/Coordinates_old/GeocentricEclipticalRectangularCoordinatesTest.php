<?php

namespace Andrmoel\AstronomyBundle\Tests\Coordinates;

use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalRectangularCoordinates;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use PHPUnit\Framework\TestCase;

class GeocentricEclipticalRectangularCoordinatesTest extends TestCase
{
    public function testGetGeocentricEclipticalSphericalCoordinates()
    {
        $X = 0.62179;
        $Y = -0.66491;
        $Z = -0.03314;

        $geoEclRecCoordinates = new GeocentricEclipticalRectangularCoordinates($X, $Y, $Z);
        $geoEclSphCoordinates = $geoEclRecCoordinates->getGeocentricEclipticalSphericalCoordinates();

        $lon = $geoEclSphCoordinates->getLongitude();
        $lat = $geoEclSphCoordinates->getLatitude();
        $radiusVector = $geoEclSphCoordinates->getRadiusVector();

        $this->assertEquals(313.08062, round($lon, 5));
        $this->assertEquals(-2.08486, round($lat, 5));
        $this->assertEquals(0.91095, round($radiusVector, 5));
    }

    public function testGetGeocentricEquatorialCoordinates()
    {
        $toi = new TimeOfInterest(new \DateTime('2002-01-31 19:54:00'));
        $X = -0.3866796199452;
        $Y = 0.90151187096352;
        $Z = 0.11495930474252;

        $geoEclRecCoordinates = new GeocentricEclipticalRectangularCoordinates($X, $Y, $Z);
        $geoEqaCoordinates = $geoEclRecCoordinates->getGeocentricEquatorialCoordinates($toi);

        $rightAscension = $geoEqaCoordinates->getRightAscension();
        $declination = $geoEqaCoordinates->getDeclination();
        $radiusVector = $geoEqaCoordinates->getRadiusVector();

        $this->assertEquals(116.328879, round($rightAscension, 6));
        $this->assertEquals(28.02594, round($declination, 6));
        $this->assertEquals(0.987654, round($radiusVector, 6));
    }
}
