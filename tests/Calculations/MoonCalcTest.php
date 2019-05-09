<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Calculations\MoonCalc;
use PHPUnit\Framework\TestCase;

class MoonCalcTest extends TestCase
{
    /**
     * @test
     * Meeus 47.a
     */
    public function getSumLTest()
    {
        $T = -0.077221081451;

        $sumL = MoonCalc::getSumL($T);

        $this->assertEquals(-1127527, round($sumL));
    }

    /**
     * @test
     * Meeus 47.a
     */
    public function getSumRTest()
    {
        $T = -0.077221081451;

        $sumR = MoonCalc::getSumR($T);

        $this->assertEquals(-16590875, round($sumR));
    }

    /**
     * @test
     * Meeus 47.a
     */
    public function getSumBTest()
    {
        $T = -0.077221081451;

        $sumB = MoonCalc::getSumB($T);

        $this->assertEquals(-3229126, round($sumB));
    }

    /**
     * @test
     * Meeus 47.a
     */
    public function getMeanElongationTest()
    {
        $T = -0.077221081451;

        $D = MoonCalc::getMeanElongation($T);

        $this->assertEquals(113.842304, round($D, 6));
    }

    /**
     * @test
     * Meeus 47.a
     */
    public function getMeanAnomalyTest()
    {
        $T = -0.077221081451;

        $Mmoon = MoonCalc::getMeanAnomaly($T);

        $this->assertEquals(5.150833, round($Mmoon, 6));
    }

    /**
     * @test
     * Meeus 47.a
     */
    public function getArgumentOfLatitudeTest()
    {
        $T = -0.077221081451;

        $F = MoonCalc::getArgumentOfLatitude($T);

        $this->assertEquals(219.889721, round($F, 6));
    }

    /**
     * @test
     * Meeus 47.a
     */
    public function getMeanLongitudeTest()
    {
        $T = -0.077221081451;

        $L = MoonCalc::getMeanLongitude($T);

        $this->assertEquals(134.290182, round($L, 6));
    }

    /**
     * @test
     * Meeus 47.a
     */
    public function getDistanceToEarthTest()
    {
        $T = -0.077221081451;

        $d = MoonCalc::getDistanceToEarth($T);

        $this->assertEquals(368409.7, round($d, 1));
    }

    /**
     * @test
     * Meeus 47.a
     */
    public function getEquatorialHorizontalParallaxTest()
    {
        $T = -0.077221081451;

        $pi = MoonCalc::getEquatorialHorizontalParallax($T);

        $this->assertEquals(0.991990, round($pi, 6));
    }

    /**
     * @test
     * Meeus 47.a
     */
    public function getLatitudeTest()
    {
        $T = -0.077221081451;

        $lat = MoonCalc::getLatitude($T);

        $this->assertEquals(-3.229126, round($lat, 6));
    }

    /**
     * @test
     * Meeus 47.a
     */
    public function getLongitudeTest()
    {
        $T = -0.077221081451;

        $lon = MoonCalc::getLongitude($T);

        $this->assertEquals(133.162655, round($lon, 6));
    }

    /**
     * @test
     * Meeus 47.a
     */
    public function getApparentLongitudeTest()
    {
        $T = -0.077221081451;

        $lon = MoonCalc::getApparentLongitude($T);

        $this->assertEquals(133.167265, round($lon, 6));
    }
}
