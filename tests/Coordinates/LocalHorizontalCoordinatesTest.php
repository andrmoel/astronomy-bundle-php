<?php

namespace Andrmoel\AstronomyBundle\Tests\Coordinates;

use Andrmoel\AstronomyBundle\Coordinates\LocalHorizontalCoordinates;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
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

        $toi = new TimeOfInterest();
        $toi->setTime(1987, 4, 10, 19, 21, 0);

        $azimuth = 68.0337;
        $altitude = 15.1249;

        $localHorizontalCoordinates = new LocalHorizontalCoordinates($azimuth, $altitude);
        $equatorialCoordinates = $localHorizontalCoordinates->getEquatorialCoordinates($location);

        $rightAscension = $equatorialCoordinates->getRightAscension();
        $declination = $equatorialCoordinates->getDeclination();

        // TODO
        $this->assertTrue(true);
//        var_dump(Util::angleDec2time($rightAscension), $declination);

//        die("DDDDDD");
    }
}
