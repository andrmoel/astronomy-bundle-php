<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

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

    public function getLongitudeOfPerihelionOfOrbitTest()
    {
        // TODO ...
    }
}
