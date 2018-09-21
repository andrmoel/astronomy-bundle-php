<?php

namespace Andrmoel\AstronomyBundle\Tests;

use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialCoordinates;
use Andrmoel\AstronomyBundle\Corrections;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use PHPUnit\Framework\TestCase;

class CorrectionsTest extends TestCase
{
    public function testCorrectCoordinates()
    {
        $toi = new TimeOfInterest(new \DateTime('2028-11-13 04:33:36'));
        $corrections = new Corrections($toi);

        $rightAscension = 41.5472;
        $declination = 49.3485;

        $geoEquCoordinates = new GeocentricEquatorialCoordinates($rightAscension, $declination);
        $geoEquCoordinates = $corrections->correctCoordinates($geoEquCoordinates);

        $rightAscension = $geoEquCoordinates->getRightAscension();
        $declination = $geoEquCoordinates->getDeclination();

        $this->assertEquals(41.55995, round($rightAscension, 5));
        $this->assertEquals(49.35209, round($declination, 5));
    }

    public function testCorrectEffectOfNutation()
    {
        $toi = new TimeOfInterest(new \DateTime('2028-11-13 04:33:00'));
        $corrections = new Corrections($toi);

        $rightAscension = 41.5472;
        $declination = 49.3485;

        $geoEquCoordinates = new GeocentricEquatorialCoordinates($rightAscension, $declination);
        $geoEquCoordinates = $corrections->correctEffectOfNutation($geoEquCoordinates);

        $rightAscension = $geoEquCoordinates->getRightAscension();
        $declination = $geoEquCoordinates->getDeclination();

        $this->assertEquals(41.55160, round($rightAscension, 5));
        $this->assertEquals(49.35023, round($declination, 5));
    }

    public function testCorrectEffectOfAberration()
    {
        $toi = new TimeOfInterest(new \DateTime('2028-11-13 04:33:00'));
        $corrections = new Corrections($toi);

        $rightAscension = 41.5472;
        $declination = 49.3485;

        $geoEquCoordinates = new GeocentricEquatorialCoordinates($rightAscension, $declination);
        $geoEquCoordinates = $corrections->correctEffectOfAberration($geoEquCoordinates);

        $rightAscension = $geoEquCoordinates->getRightAscension();
        $declination = $geoEquCoordinates->getDeclination();

        $this->assertEquals(41.55555, round($rightAscension, 5));
        $this->assertEquals(49.35036, round($declination, 5));
    }
}
