<?php

namespace Andrmoel\AstronomyBundle\Tests\Coordinates;

use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialCoordinates;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;
use PHPUnit\Framework\TestCase;

class GeocentricEquatorialCoordinatesTest extends TestCase
{
    /**
     * Meeus 13.a
     */
    public function testGetEclipticalCoordinates()
    {
        $rightAscension = 116.328942;
        $declination = 28.026183;
        $radiusVector = 0.987654;
        $eps = 23.4392911;

        $geoEquCoordinates = new GeocentricEquatorialCoordinates($rightAscension, $declination, $radiusVector);
        $geoEclSphCoordinates = $geoEquCoordinates->getGeocentricEclipticalSphericalCoordinates($eps);

        $lat = $geoEclSphCoordinates->getLatitude();
        $lon = $geoEclSphCoordinates->getLongitude();
        $radiusVector = $geoEclSphCoordinates->getRadiusVector();

        $this->assertEquals(6.684170, round($lat, 6));
        $this->assertEquals(113.215630, round($lon, 6));
        $this->assertEquals(0.987654, round($radiusVector, 6));
    }

    /**
     * Meeus 13.b
     */
    public function testGetLocalHorizontalCoordinates()
    {
        $lat = 38.92139;
        $lon = -77.06556;
        $location = new Location($lat, $lon);

        $toi = new TimeOfInterest();
        $toi->setTime(1987, 4, 10, 19, 21, 0);

        $rightAscension = AngleUtil::time2dec('23h9m16.641s');
        $declination = AngleUtil::angle2dec('-6°43\'11.61"');

        $geoEquCoordinates = new GeocentricEquatorialCoordinates($rightAscension, $declination);
        $localHorizontalCoordinates = $geoEquCoordinates->getLocalHorizontalCoordinates($location, $toi);

        $azimuth = $localHorizontalCoordinates->getAzimuth();
        $altitude = $localHorizontalCoordinates->getAltitude();

        $this->assertEquals(248.0336, round($azimuth, 4));
        $this->assertEquals(15.125, round($altitude, 4));
    }
}
