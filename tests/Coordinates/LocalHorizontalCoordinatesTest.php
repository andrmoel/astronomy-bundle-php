<?php

namespace Andrmoel\AstronomyBundle\Tests\Complex;

use Andrmoel\AstronomyBundle\Coordinates\LocalHorizontalCoordinates;
use Andrmoel\AstronomyBundle\Location;
use PHPUnit\Framework\TestCase;

class LocalHorizontalCoordinatesTest extends TestCase
{
    /**
     * Meeus 13.b
     */
    public function testGetEquatorialCoordinates()
    {
        $lat = 38.92139;
        $lon = -77.06556;
        $location = new Location($lat, $lon);

        $azimuth = 68.0318;
        $altitude = 28.026183;

        $localHorizontalCoordinates = new LocalHorizontalCoordinates($azimuth, $altitude);
        $equatorialCoordinates = $localHorizontalCoordinates->getEquatorialCoordinates($location);

        $rightAscension = $equatorialCoordinates->getRightAscension();
        $declination = $equatorialCoordinates->getDeclination();

        var_dump($rightAscension, $declination);

        die("DDDDDD");
    }
}
