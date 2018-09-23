<?php

namespace Andrmoel\AstronomyBundle\Tests;

use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialCoordinates;
use Andrmoel\AstronomyBundle\Corrections;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;
use PHPUnit\Framework\TestCase;

class CorrectionsTest extends TestCase
{
    /*
     * Meeus 23.a
     */
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

        var_dump(AngleUtil::dec2time($rightAscension), AngleUtil::dec2angle($declination));die();

        $this->assertEquals(41.55995, round($rightAscension, 5));
        $this->assertEquals(49.35209, round($declination, 5));
    }

    public function XtestCorrectEffectOfPrecession()
    {
        $toi = new TimeOfInterest(new \DateTime('2028-11-13 04:33:34'));
        $corrections = new Corrections($toi);

        $rightAscension = 152.093;
        $declination = 11.967;

        $geoEquCoordinates = new GeocentricEquatorialCoordinates($rightAscension, $declination);
        $geoEquCoordinates = $corrections->correctEffectOfPrecession($geoEquCoordinates);

        $rightAscension = $geoEquCoordinates->getRightAscension();
        $declination = $geoEquCoordinates->getDeclination();

        // TODO...
//        var_dump(AngleUtil::dec2time($rightAscension), AngleUtil::dec2angle($declination));die();
    }

    /*
     * Meeus 23.a
     */
    public function XtestCorrectEffectOfNutation()
    {
        $toi = new TimeOfInterest(new \DateTime('2028-11-13 04:33:34'));
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

    /*
     * Meeus 23.a
     */
    public function XtestCorrectEffectOfAberration()
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

    public function XtestCorrectVanFoo()
    {
        $toi = new TimeOfInterest(new \DateTime('2028-11-13 04:33:37'));
        $corrections = new Corrections($toi);

        var_dump($toi->getJulianCenturiesFromJ2000());
        $rightAscension = 41.0540613;
        $declination = 49.2277489;
        $geoEquCoordinates = new GeocentricEquatorialCoordinates($rightAscension, $declination);

        $geoEquCoordinates = $corrections->correctWithRonVondrakExpression($geoEquCoordinates);

        var_dump($geoEquCoordinates->getRightAscension(), $geoEquCoordinates->getDeclination());die();
    }
}
