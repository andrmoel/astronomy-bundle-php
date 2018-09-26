<?php

namespace Andrmoel\AstronomyBundle\Tests\Corrections;

use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialCoordinates;
use Andrmoel\AstronomyBundle\Corrections\GeocentricEquatorialCorrections;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use PHPUnit\Framework\TestCase;

class GeocentricEquatorialCorrectionsTest extends TestCase
{
    /**
     * Meeus 23.b
     */
    public function testCorrectCoordinates()
    {
        $toi = new TimeOfInterest(new \DateTime('2028-11-13 04:33:36'));
        $rightAscension = 41.0540613;
        $declination = 49.2277489;
        $radiusVector = 1.23456;

        $geoEclSphCoordinates = new GeocentricEquatorialCoordinates($rightAscension, $declination, $radiusVector);
        $corrections = new GeocentricEquatorialCorrections($toi);

        $geoEclSphCoordinates = $corrections->correctCoordinates($geoEclSphCoordinates);

        $rightAscension = $geoEclSphCoordinates->getRightAscension();
        $declination = $geoEclSphCoordinates->getDeclination();
        $radiusVector = $geoEclSphCoordinates->getRadiusVector();

        $this->assertEquals(41.0667666, round($rightAscension, 7));
        $this->assertEquals(49.2313559, round($declination, 7));
        $this->assertEquals(1.23456, round($radiusVector, 5));

        // Values with precession
//        $this->assertEquals(41.5599744, round($rightAscension, 7));
//        $this->assertEquals(49.3520484, round($declination, 7));
//        $this->assertEquals(1.23456, round($radiusVector, 5));
    }

    /**
     * Meeus 23.b
     */
    public function testCorrectEffectOfPrecession()
    {
        $toi = new TimeOfInterest(new \DateTime('2028-11-13 04:33:36'));
        $rightAscension = 41.0623836;
        $declination = 49.2296238;
        $radiusVector = 1.23456;

        $geoEclSphCoordinates = new GeocentricEquatorialCoordinates($rightAscension, $declination, $radiusVector);
        $corrections = new GeocentricEquatorialCorrections($toi);

        $geoEclSphCoordinates = $corrections->correctEffectOfPrecession($geoEclSphCoordinates);

        $rightAscension = $geoEclSphCoordinates->getRightAscension();
        $declination = $geoEclSphCoordinates->getDeclination();
        $radiusVector = $geoEclSphCoordinates->getRadiusVector();

        $this->assertEquals(41.5555635, round($rightAscension, 7));
        $this->assertEquals(49.3503415, round($declination, 7));
        $this->assertEquals(1.23456, round($radiusVector, 5));
    }

    /**
     * Meeus 23.b
     */
    public function testCorrectEffectOfNutation()
    {
        $toi = new TimeOfInterest(new \DateTime('2028-11-13 04:33:36'));
        $rightAscension = 41.5555635;
        $declination = 49.3503415;
        $radiusVector = 1.23456;

        $geoEclSphCoordinates = new GeocentricEquatorialCoordinates($rightAscension, $declination, $radiusVector);
        $corrections = new GeocentricEquatorialCorrections($toi);

        $geoEclSphCoordinates = $corrections->correctEffectOfNutation($geoEclSphCoordinates);

        $rightAscension = $geoEclSphCoordinates->getRightAscension();
        $declination = $geoEclSphCoordinates->getDeclination();
        $radiusVector = $geoEclSphCoordinates->getRadiusVector();

        $this->assertEquals(41.5599647, round($rightAscension, 7));
        $this->assertEquals(49.3520685, round($declination, 7));
        $this->assertEquals(1.23456, round($radiusVector, 5));
    }

    /**
     * Meeus 23.b
     */
    public function testCorrectEffectOfAberration()
    {
        $toi = new TimeOfInterest(new \DateTime('2028-11-13 04:33:36'));
        $rightAscension = 41.0540613;
        $declination = 49.2277489;
        $radiusVector = 1.23456;

        $geoEclSphCoordinates = new GeocentricEquatorialCoordinates($rightAscension, $declination, $radiusVector);
        $corrections = new GeocentricEquatorialCorrections($toi);

        $geoEclSphCoordinates = $corrections->correctEffectOfAberration($geoEclSphCoordinates);

        $rightAscension = $geoEclSphCoordinates->getRightAscension();
        $declination = $geoEclSphCoordinates->getDeclination();
        $radiusVector = $geoEclSphCoordinates->getRadiusVector();

        $this->assertEquals(41.0623852, round($rightAscension, 7));
        $this->assertEquals(49.2296247, round($declination, 7));
        $this->assertEquals(1.23456, round($radiusVector, 5));
    }
}
