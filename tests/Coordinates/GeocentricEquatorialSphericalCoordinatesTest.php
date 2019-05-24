<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialSphericalCoordinates;
use Andrmoel\AstronomyBundle\Location;
use PHPUnit\Framework\TestCase;

class GeocentricEquatorialSphericalCoordinatesTest extends TestCase
{
    /**
     * @test
     * Meeus 13.a
     */
    public function getGeocentricEclipticalSphericalCoordinatesTest()
    {
        $T = 0.021;
        $ra = 116.328942;
        $d = 28.026183;

        $geoEquSphCoord = new GeocentricEquatorialSphericalCoordinates($ra, $d);
        $geoEclSphCoord = $geoEquSphCoord->getGeocentricEclipticalSphericalCoordinates($T);

        $lat = $geoEclSphCoord->getLatitude();
        $lon = $geoEclSphCoord->getLongitude();

        $this->assertEquals(6.684170, round($lat, 6));
        $this->assertEquals(113.215630, round($lon, 6));
    }

    public function getGeocentricEquatorialRectangularCoordinatesTest()
    {
        // TODO ...
    }

    /**
     * @test
     * Meeus 13.b
     */
    public function getLocalHorizontalCoordinatesTest()
    {
        $location = new Location(38.921389, -77.065556);
        $T = -0.12727429842574; // 1987-04-10 19:21:00
        $ra = 347.3193375; // 23h09m16.641s
        $d = -6.719891667; // 6°43'11.61"

        $geoEquSphCoord = new GeocentricEquatorialSphericalCoordinates($ra, $d);
        $locHorCoord = $geoEquSphCoord->getLocalHorizontalCoordinates($location, $T);

        $azimuth = $locHorCoord->getAzimuth();
        $altitude = $locHorCoord->getAltitude();

        $this->assertEquals(248.0336, round($azimuth, 4));
        $this->assertEquals(15.125, round($altitude, 4));
    }
}
