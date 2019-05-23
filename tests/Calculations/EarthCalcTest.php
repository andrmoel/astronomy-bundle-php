<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Calculations\EarthCalc;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;
use PHPUnit\Framework\TestCase;

class EarthCalcTest extends TestCase
{
    /**
     * @test
     * Meeus 22.a
     */
    public function getMeanAnomalyTest()
    {
        $T = -0.127296372348;

        $M = EarthCalc::getMeanAnomaly($T);

        $this->assertEquals(94.9806, round($M, 4));
    }

    /**
     * @test
     * Meeus 25.a
     */
    public function getEccentricityTest()
    {
        $T = -0.072183436;

        $e = EarthCalc::getEccentricity($T);

        $this->assertEquals(0.016711668, round($e, 9));
    }

    /**
     * @test
     * Meeus 23.a
     */
    public function getLongitudeOfPerihelionOfOrbitTest()
    {
        $T = 0.2886705;

        $pi = EarthCalc::getLongitudeOfPerihelionOfOrbit($T);

        $this->assertEquals(103.434, round($pi, 3));
    }

    /**
     * @test
     * Meeus 22.a
     */
    public function getMeanObliquityOfEclipticTest()
    {
        $T = -0.127296372458;

        $e0 = EarthCalc::getMeanObliquityOfEcliptic($T);

        $this->assertEquals(23.44094629, round($e0, 8));
    }

    /**
     * @test
     * Meeus 22.a
     */
    public function getTrueMeanObliquityOfEclipticTest()
    {
        $T = -0.127296372458;

        $e = EarthCalc::getTrueObliquityOfEcliptic($T);

        $this->assertEquals(23.4435692, round($e, 8));
    }

    /**
     * @test
     * Meeus 22.a
     */
    public function getNutationInLongitudeTest()
    {
        $T = -0.127296372458;

        $sumPhi = EarthCalc::getNutationInLongitude($T);

        $this->assertStringStartsWith('-0°0\'3.788', AngleUtil::dec2angle($sumPhi));
    }

    /**
     * @test
     * Meeus 22.a
     */
    public function getNutationInObliquityTest()
    {
        $T = -0.127296372458;

        $sumEps = EarthCalc::getNutationInObliquity($T);

        $this->assertStringStartsWith('0°0\'9.442', AngleUtil::dec2angle($sumEps));
    }

    /**
     * @test
     * Meeus 28.a
     */
    public function getEquationOfTimeInMinutesTest()
    {
        $T = -0.072183436;

        $E = EarthCalc::getEquationOfTimeInMinutes($T);

        $this->assertEquals(13.69883, round($E, 5));

        // TODO Use method with higher accuracy (Meeus p.166) 25.9
//        $this->assertEquals(13.70941, round($E, 5));
    }
}
