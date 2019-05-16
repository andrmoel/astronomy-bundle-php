<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Coordinates\LocalHorizontalCoordinates;
use Andrmoel\AstronomyBundle\Location;
use PHPUnit\Framework\TestCase;

class LocalHorizontalCoordinatesTest extends TestCase
{
    /**
     * @test
     * Meeus 13.b
     */
    public function getGeocentricEquatorialSphericalCoordinatesTest()
    {
        $location = new Location(38.921389, -77.065556);
        $T = -0.12727429842574; // 1987-04-10 19:21:00
        $azimuth = 68.0337;
        $altitude = 15.1249;

        $locHorCoord = new LocalHorizontalCoordinates($azimuth, $altitude);
        $geoEquCoord = $locHorCoord->getGeocentricEquatorialSphericalCoordinates($location, $T);

        $ra = $geoEquCoord->getRightAscension();
        $d = $geoEquCoord->getDeclination();

        $this->assertEquals(347.31921, round($ra, 5));
        $this->assertEquals(-6.71987, round($d, 5));
    }
}
