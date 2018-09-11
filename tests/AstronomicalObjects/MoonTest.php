<?php

namespace Andrmoel\AstronomyBundle\Tests\AstronomicalObjects;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Moon;
use Andrmoel\AstronomyBundle\Coordinates\EclipticalCoordinates;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use PHPUnit\Framework\TestCase;

class MoonTest extends TestCase
{
    /**
     * Meeus 47.a
     */
    public function testGetEclipticalCoordinates()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(1992, 4, 12, 0, 0, 0);

        $moon = new Moon();
        $moon->setTimeOfInterest($toi);
        $eclipticalCoordinates = $moon->getEclipticalCoordinates();

        $latitude = $eclipticalCoordinates->getLatitude();
        $longitude = $eclipticalCoordinates->getLongitude();

        $this->assertEquals(-3.229126, round($latitude, 6));
        $this->assertEquals(133.162655, round($longitude, 6)); // TODO Failed...
    }

    /**
     * Meeus 47.a
     */
    public function testGetDistanceToEarth()
    {
        $toi = new TimeOfInterest();
        $toi->setTime(1992, 4, 12, 0, 0, 0);

        $moon = new Moon();
        $moon->setTimeOfInterest($toi);
        $distance = $moon->getDistanceToEarth();

        $this->assertEquals(36840968, round($distance));
    }
}
