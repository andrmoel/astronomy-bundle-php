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
        $radiusVector = 0.910947;

        $geoEclSphCoordinates = new GeocentricEclipticalSphericalCoordinates($lon, $lat, $radiusVector);
        $geoEclRecCoordinates = $geoEclSphCoordinates->getGeocentricEclipticalRectangularCoordinates();

        $X = $geoEclRecCoordinates->getX();
        $Y = $geoEclRecCoordinates->getY();
        $Z = $geoEclRecCoordinates->getZ();

        $this->assertEquals(0.621794, round($X, 6));
        $this->assertEquals(-0.664905, round($Y, 6));
        $this->assertEquals(-0.033138, round($Z, 6));
    }

    /**
     * Meeus 13.a
     */
    public function testGetGeocentricEquatorialCoordinates()
    {
        $toi = new TimeOfInterest(new \DateTime()); // TODO ...
        $lat = 6.684170;
        $lon = 113.215630;
        $radiusVector = 0.987654;
        $eps = 23.4392911;

        $geoEclSphCoordinates = new GeocentricEclipticalSphericalCoordinates($lon, $lat, $radiusVector);
        $geoEquCoordinates = $geoEclSphCoordinates->getGeocentricEquatorialCoordinates($toi);

        $rightAscension = $geoEquCoordinates->getRightAscension();
        $declination = $geoEquCoordinates->getDeclination();
        $radiusVector = $geoEquCoordinates->getRadiusVector();

        $this->assertEquals(116.328943, round($rightAscension, 6));
        $this->assertEquals(28.026183, round($declination, 6));
        $this->assertEquals(0.987654, round($radiusVector, 6));
    }
}
