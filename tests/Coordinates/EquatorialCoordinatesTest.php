<?php

namespace Andrmoel\AstronomyBundle\Tests\Coordinates;

use Andrmoel\AstronomyBundle\Coordinates\EquatorialCoordinates;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use PHPUnit\Framework\TestCase;

class EquatorialCoordinatesTest extends TestCase
{
    /**
     * Meeus 13.a
     */
    public function testGetEclipticalCoordinates()
    {
        $rightAscension = 116.328942;
        $declination = 28.026183;
        $eps = 23.4392911;

        $equatorialCoordinates = new EquatorialCoordinates($rightAscension, $declination);
        $eclipticalCoordinates = $equatorialCoordinates->getEclipticalCoordinates($eps);

        $lat = $eclipticalCoordinates->getLatitude();
        $lon = $eclipticalCoordinates->getLongitude();

        $this->assertEquals(6.684170, round($lat, 6));
        $this->assertEquals(113.215630, round($lon, 6));
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

        $rightAscension = 347.31933;
        $declination = -6.71989;

        $equatorialCoordinates = new EquatorialCoordinates($rightAscension, $declination);
        $localHorizontalCoordinates = $equatorialCoordinates->getLocalHorizontalCoordinates($location, $toi);

        $azimuth = $localHorizontalCoordinates->getAzimuth();
        $altitude = $localHorizontalCoordinates->getAltitude();

        $this->assertEquals(68.0317, round($azimuth, 4));
        $this->assertEquals(15.1269, round($altitude, 4));
    }
}
