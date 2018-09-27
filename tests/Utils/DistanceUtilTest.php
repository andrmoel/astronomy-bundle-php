<?php

namespace Andrmoel\AstronomyBundle\Tests\Utils;

use Andrmoel\AstronomyBundle\Utils\DistanceUtil;
use PHPUnit\Framework\TestCase;

class DistanceUtilTest extends TestCase
{
    public function testAu2km()
    {
        $this->assertEquals(149597870.7, round(DistanceUtil::au2km(1.0), 1));
        $this->assertEquals(5086327603.8, round(DistanceUtil::au2km(34.0), 1));
    }

    public function testKm2au()
    {
        $this->assertEquals(1.0, round(DistanceUtil::km2au(149597870.7), 1));
        $this->assertEquals(34.0, round(DistanceUtil::km2au(5086327603.8), 1));
    }
}
