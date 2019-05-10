<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Calculations\SunCalc;
use PHPUnit\Framework\TestCase;

class SunCalcTest extends TestCase
{
    /**
     * @test
     * Meeus 22.a
     */
    public function getMeanAnomalyTest()
    {
        $T = -0.127296372348;

        $M = SunCalc::getMeanAnomaly($T);

        $this->assertEquals(94.9806, round($M, 4));
    }

    /**
     * @test
     * Meeus 25.a
     */
    public function getTrueAnomalyTest()
    {
        $T = -0.072183436002738;

        $v = SunCalc::getTrueAnomaly($T);

        $this->assertEquals(277.09664, round($v, 5));
    }

    /**
     * @test
     * Meeus 25.a
     */
    public function getMeanLongitudeTest()
    {
        $T = -0.072183436002738;

        $L0 = SunCalc::getMeanLongitude($T);

        $this->assertEquals(201.80719, round($L0, 5));
    }

    /**
     * @test
     * Meeus 25.a
     */
    public function getTrueLongitudeTest()
    {
        $T = -0.072183436;

        $o = SunCalc::getTrueLongitude($T);

        $this->assertEquals(199.90987, round($o, 5));
    }

    /**
     * @test
     * Meeus 25.a
     */
    public function getApparentLongitudeTest()
    {
        $T = -0.072183436;

        $o = SunCalc::getApparentLongitude($T);

        $this->assertEquals(199.90894, round($o, 5));
    }

    /**
     * @test
     * Meeus 25.a
     */
    public function getEquationOfCenterTest()
    {
        $T = -0.072183436002738;

        $C = SunCalc::getEquationOfCenter($T);

        $this->assertEquals(-1.89732, round($C, 5));
    }

    /**
     * @test
     * Meeus 25.a
     */
    public function getRadiusVectorTest()
    {
        $T = -0.072183436002738;

        $R = SunCalc::getRadiusVector($T);

        $this->assertEquals(0.99766, round($R, 5));
    }

    /**
     * @test
     */
    public function getDistanceToEarthTest()
    {
        $T = -0.072183436002738;

        $r = SunCalc::getDistanceToEarth($T);

        $this->assertEquals(149248103.44, round($r, 2));
    }

    /**
     * @test
     * Meeus 25.a
     */
    public function getApparentRightAscensionTest()
    {
        $T = -0.072183436002738;

        $rightAscension = SunCalc::getApparentRightAscension($T);

        $this->assertEquals(198.38082, round($rightAscension, 5));
    }

    /**
     * @test
     * Meeus 25.a
     */
    public function getApparentDeclinationTest()
    {
        $T = -0.072183436002738;

        $declination = SunCalc::getApparentDeclination($T);

        $this->assertEquals(-7.78507, round($declination, 5));
    }
}
