<?php

namespace Andrmoel\AstronomyBundle\Tests\Coordinates;

use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalSphericalCoordinates;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use PHPUnit\Framework\TestCase;

class GeocentricEclipticalSphericalCoordinatesTest extends TestCase
{
    public function testGetGeocentricEclipticalRectangularCoordinates()
    {
        $lon = 313.08102;
        $lat = -2.08474;
        $radiusVector = 0.91095;

        $geoEclSphCoordinates = new GeocentricEclipticalSphericalCoordinates($lon, $lat, $radiusVector);
        $geoEclRecCoordinates = $geoEclSphCoordinates->getGeocentricEclipticalRectangularCoordinates();

        $X = $geoEclRecCoordinates->getX();
        $Y = $geoEclRecCoordinates->getY();
        $Z = $geoEclRecCoordinates->getZ();

        $this->assertEquals(0.6218, round($X, 5));
        $this->assertEquals(-0.66491, round($Y, 5));
        $this->assertEquals(-0.03314, round($Z, 5));
    }

    /**
     * Meeus 13.a
     */
    public function testGetGeocentricEquatorialCoordinates()
    {
        $toi = new TimeOfInterest(new \DateTime('2002-01-31 19:54:00'));
        $lon = 113.215630;
        $lat = 6.684170;
        $radiusVector = 0.987654;

        $geoEclSphCoordinates = new GeocentricEclipticalSphericalCoordinates($lon, $lat, $radiusVector);
        $geoEquCoordinates = $geoEclSphCoordinates->getGeocentricEquatorialCoordinates($toi);

        $rightAscension = $geoEquCoordinates->getRightAscension();
        $declination = $geoEquCoordinates->getDeclination();
        $radiusVector = $geoEquCoordinates->getRadiusVector();

        $this->assertEquals(116.328879, round($rightAscension, 6));
        $this->assertEquals(28.02594, round($declination, 6));
        $this->assertEquals(0.987654, round($radiusVector, 6));

        // TODO
//        $this->assertEquals(116.328943, round($rightAscension, 6));
//        $this->assertEquals(28.026183, round($declination, 6));
//        $this->assertEquals(0.987654, round($radiusVector, 6));
    }
}
