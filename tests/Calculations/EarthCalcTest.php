<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Earth;
use Andrmoel\AstronomyBundle\Calculations\EarthCalc;
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
        // TODO Mocking
//        $mock = $this->getMockClass(EarthCalc::class, array('getMeanObliquityOfEcliptic'));
//        $mock::staticExpects($this->one())
//            ->method('getMeanObliquityOfEcliptic')
//            ->will($this->returnValue(1234));
//
//        $T = -0.127296372458;
//
//        $e = EarthCalc::getTrueObliquityOfEcliptic($T);
    }
}
