<?php

namespace Andrmoel\AstronomyBundle\Tests\Coordinates;

use Andrmoel\AstronomyBundle\Coordinates\EclipticalCoordinates;
use PHPUnit\Framework\TestCase;

class EclipticalCoordinatesTest extends TestCase
{
    /**
     * Meeus 13.a
     */
    public function testGetEquatorialCoordinates()
    {
        $lat = 6.684170;
        $lon = 113.215630;
        $eps = 23.4392911;

        $eclipticalCoordinates = new EclipticalCoordinates($lat, $lon);
        $equatorialCoordinates = $eclipticalCoordinates->getEquatorialCoordinates($eps);

        $rightAscension = $equatorialCoordinates->getRightAscension();
        $declination = $equatorialCoordinates->getDeclination();

        $this->assertEquals(116.328943, round($rightAscension, 6));
        $this->assertEquals(28.026183, round($declination, 6));
    }
}
