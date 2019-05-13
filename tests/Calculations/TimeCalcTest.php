<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Calculations\TimeCalc;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;
use PHPUnit\Framework\TestCase;

class TimeCalcTest extends TestCase
{
    /**
     * @test
     * Meeus 12.a
     */
    public function getGreenwichMeanSiderealTimeTest()
    {
        $T = -0.127296372348; // 1987-04-10 00:00:00

        $GMST = TimeCalc::getGreenwichMeanSiderealTime($T);
        $GMST = AngleUtil::dec2time($GMST);

        $this->assertEquals('13h10m46.366s', $GMST);

        $T = -0.12727429842574; // 1987-04-10 19:21:00

        $GMST = TimeCalc::getGreenwichMeanSiderealTime($T);
        $GMST = AngleUtil::dec2time($GMST);

        $this->assertEquals('8h34m57.09s', $GMST);
    }

    /**
     * @test
     * Meeus 12.a
     */
    public function getGreenwichApparentSiderealTimeTest()
    {
        $T = -0.127296372348;

        $GAST = TimeCalc::getGreenwichApparentSiderealTime($T);
        $GAST = AngleUtil::dec2time($GAST);

        $this->assertEquals('13h10m46.134s', $GAST);
    }

    /**
     * @test
     * Meeus Table 10.A
     */
    public function getDeltaTTest()
    {
        $array = [
//            2050 => 93,
            2018 => 70.5,
            1996 => 61.6,
            1990 => 56.9,
            1986 => 54.9,
            1980 => 50.5,
            1970 => 40.2,
            1960 => 33.1,
            1950 => 29.1,
            1940 => 24.4,
            1930 => 24.1,
            1920 => 21.2,
            1910 => 10.3,
            1900 => -2.9,
            1890 => -6.1,
            1880 => -5.0,
            1870 => 1.0,
            1860 => 7.6,
            1850 => 7.1,
            1840 => 5.5,
            1830 => 7.7,
            1820 => 11.9,
            1810 => 12.5,
            1800 => 13.7,
            1750 => 13.4,
            1700 => 8.8,
            1650 => 50.3,
            1630 => 80.6,
            1620 => 95.4,
            1600 => 120,
            1400 => 321.8,
            1200 => 736.6,
            1000 => 1574.4,
            800 => 2956,
            600 => 4739.6,
            400 => 6699.6,
            200 => 8641.1,
            0 => 10584,
            -200 => 12792.7,
            -400 => 15531.6,
            -600 => 18721.1,
            -800 => 21946.8,
            -1000 => 25428.4,
        ];

        foreach ($array as $year => $expectedDeltaT) {
            $deltaT = TimeCalc::getDeltaT($year);

            $this->assertEquals($expectedDeltaT, round($deltaT, 1));
        }

    }
}
