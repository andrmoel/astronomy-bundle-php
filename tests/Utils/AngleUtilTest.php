<?php

namespace Andrmoel\AstronomyBundle\Tests\Utils;

use Andrmoel\AstronomyBundle\Utils\AngleUtil;
use PHPUnit\Framework\TestCase;

class AngleUtilTest extends TestCase
{
    public function XtestAngle2dec()
    {
        $this->assertEquals(0.0, AngleUtil::angle2dec('0°0\'0"'));
        $this->assertEquals(45.2625, AngleUtil::angle2dec('45°15\'45"'));
        $this->assertEquals(45.2625, AngleUtil::angle2dec('45° 15\' 45"'));
        $this->assertEquals(270.5, AngleUtil::angle2dec('270°30\'0"'));
        $this->assertEquals(-0.001052, round(AngleUtil::angle2dec('-0°0\'3.788"'), 6));
    }

    public function testDec2angle()
    {
        $this->assertEquals('0°0\'0"', AngleUtil::dec2angle(0.0));
        $this->assertEquals('45°15\'45"', AngleUtil::dec2angle(45.2625));
        $this->assertEquals('270°30\'0"', AngleUtil::dec2angle(270.5));
        $this->assertEquals('-0°0\'3.788"', AngleUtil::dec2angle(-0.00105222));
        $this->assertEquals('-24°55\'45.524"', AngleUtil::dec2angle(-24.929312194388));
    }

    public function XtestDec2time()
    {
        $this->assertEquals('0h0m0s', AngleUtil::dec2time(0.0));
        $this->assertEquals('3h1m36s', AngleUtil::dec2time(45.4));
        $this->assertEquals('24h0m0s', AngleUtil::dec2time(360.0));
         $this->assertEquals('-3h1m36s', AngleUtil::dec2time(-45.4));
    }

    public function XtestTime2dec()
    {
        $this->assertEquals(0.0, AngleUtil::time2dec('0h0m0s'));
        $this->assertEquals(45.4, AngleUtil::time2dec('3h1m36s'));
        $this->assertEquals(45.4, AngleUtil::time2dec('3h 1m 36s'));
        $this->assertEquals(360.0, AngleUtil::time2dec('24h0m0s'));
        $this->assertEquals(191.3146, round(AngleUtil::time2dec('12h45m15.512s'), 4));
    }

    public function XtestNormalizeAngle()
    {
        $this->assertEquals(0.0, AngleUtil::normalizeAngle(0.0));
        $this->assertEquals(12.5, AngleUtil::normalizeAngle(12.5));
        $this->assertEquals(359.0, AngleUtil::normalizeAngle(359.0));
        $this->assertEquals(0.0, AngleUtil::normalizeAngle(360.0));
        $this->assertEquals(204.30, AngleUtil::normalizeAngle(5964.30));
        $this->assertEquals(315.0, AngleUtil::normalizeAngle(-45.0));
        $this->assertEquals(79.0, AngleUtil::normalizeAngle(259.0, 180.0));
    }
}
