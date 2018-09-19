<?php

namespace Andrmoel\AstronomyBundle\Tests\Coordinates;

use Andrmoel\AstronomyBundle\Coordinates\HeliocentricEclipticalCoordinates;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use PHPUnit\Framework\TestCase;

class HeliocentricEclipticalCoordinatesTest extends TestCase
{
    /**
     * Meeus 33.a
     */
    public function testGetEquatorialRectangularCoordinates()
    {
        $L = 26.11428;
        $B = -2.62070;
        $R = 0.724603;

        $toi = new TimeOfInterest(new \DateTime('1992-12-20 00:00:00'));

        $heliocentricEclipticalCoordinates = new HeliocentricEclipticalCoordinates($L, $B, $R);
        $equatorialRectangularCoordinates = $heliocentricEclipticalCoordinates
            ->getEquatorialRectangularCoordinates($toi);

        $X = $equatorialRectangularCoordinates->getX();
        $Y = $equatorialRectangularCoordinates->getY();
        $Z = $equatorialRectangularCoordinates->getZ();

        $this->assertEquals(0.621746, round($X, 6));
        $this->assertEquals(-0.664810, round($Y, 6));
        $this->assertEquals(-0.033134, round($Z, 6));
    }

    /**
     * Meeus 33.a
     */
    public function testGetEclipticalCoordinates()
    {
        $L = 26.11428;
        $B = -2.62070;
        $R = 0.724603;

        $toi = new TimeOfInterest(new \DateTime('1992-12-20 00:00:00'));

        $heliocentricEclipticalCoordinates = new HeliocentricEclipticalCoordinates($L, $B, $R);
        $eclipticalCoordinates = $heliocentricEclipticalCoordinates->getEclipticalCoordinates($toi);

        $latitude = $eclipticalCoordinates->getLatitude();
        $longitude = $eclipticalCoordinates->getLongitude();

        $this->assertEquals(-2.08473, round($latitude, 5));
        $this->assertEquals(313.08289, round($longitude, 5));
    }
}
