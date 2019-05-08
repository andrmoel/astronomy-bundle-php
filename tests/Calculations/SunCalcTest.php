<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Calculations\SunCalc;
use PHPUnit\Framework\TestCase;

class SunCalcTest extends TestCase
{
    /**
     * @test
     * Meeus 28.a // TODO ?
     */
    public function getMeanLongitudeTest()
    {
        $T = 0; // TODO ...

        $L0 = SunCalc::getMeanLongitude($T);

        $this->assertEquals(0.0, $L0);
    }
}
