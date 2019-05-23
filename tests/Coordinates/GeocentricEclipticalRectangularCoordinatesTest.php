<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalRectangularCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalSphericalCoordinates;
use PHPUnit\Framework\TestCase;

class GeocentricEclipticalRectangularCoordinatesTest extends TestCase
{
    /**
     * @test
     */
    public function getGeocentricEclipticalSphericalCoordinatesTest()
    {
        $X = 0.621746;
        $Y = -0.664810;
        $Z = -0.033134;

        $geoEclRecCoord = new GeocentricEclipticalRectangularCoordinates($X, $Y, $Z);
        $geoEclSphCoord = $geoEclRecCoord->getGeocentricEclipticalSphericalCoordinates();

        $lat = $geoEclSphCoord->getLatitude();
        $lon = $geoEclSphCoord->getLongitude();
        $r = $geoEclSphCoord->getRadiusVector();

        $this->assertEquals(-2.084721, round($lat, 6));
        $this->assertEquals(313.082894, round($lon, 6));
        $this->assertEquals(0.910845, round($r, 6));
    }
}
