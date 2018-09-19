<?php

namespace Andrmoel\AstronomyBundle\Tests\Coordinates;

use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalSphericalCoordinates;
use PHPUnit\Framework\TestCase;

class GeocentricEclipticalSphericalCoordinatesTest extends TestCase
{
    /**
     * Meeus 13.a
     */
    public function testGetGeocentricEquatorialCoordinates()
    {
        $lat = 6.684170;
        $lon = 113.215630;
        $radiusVector = 0.987654;
        $eps = 23.4392911;

        $geoEclSphCoordinates = new GeocentricEclipticalSphericalCoordinates($lon, $lat, $radiusVector);
        $geoEquCoordinates = $geoEclSphCoordinates->getGeocentricEquatorialCoordinates($eps);

        $rightAscension = $geoEquCoordinates->getRightAscension();
        $declination = $geoEquCoordinates->getDeclination();
        $radiusVector = $geoEquCoordinates->getRadiusVector();

        $this->assertEquals(116.328943, round($rightAscension, 6));
        $this->assertEquals(28.026183, round($declination, 6));
        $this->assertEquals(0.987654, round($radiusVector, 6));
    }
}
