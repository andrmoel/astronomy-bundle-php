<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Calculations\TimeCalc;
use Andrmoel\AstronomyBundle\Entities\AstroDateTime;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;
use PHPUnit\Framework\TestCase;

class TimeCalcTest extends TestCase
{
    /**
     * @test
     */
    public function julianDay2JulianDay0Test()
    {
        $data = [
            [2451545.0, 2451544.5],
            [2447187.5, 2447187.5],
            [2026871.83, 2026871.5],
            [2026871.3, 2026870.5],
            [1, 0.5],
        ];

        foreach ($data as $t) {
            $JD0 = TimeCalc::julianDay2JulianDay0($t[0]);

            $this->assertEquals($t[1], $JD0);
        }
    }

    /**
     * @test
     * Meeus 7a
     */
    public function dateTime2JulianDayTest()
    {
        $data = array(
            [2000, 1, 1, 12, 0, 0, 2451545.0],
            [1999, 1, 1, 0, 0, 0, 2451179.5],
            [1987, 1, 27, 0, 0, 0, 2446822.5],
            [1987, 6, 19, 12, 0, 0, 2446966.0],
            [1988, 1, 27, 0, 0, 0, 2447187.5],
            [1988, 6, 19, 12, 0, 0, 2447332.0],
            [1900, 1, 1, 0, 0, 0, 2415020.5],
            [1600, 1, 1, 0, 0, 0, 2305447.5],
            [1600, 12, 31, 0, 0, 0, 2305812.5],
            [837, 4, 10, 8, 0, 0, 2026871.83],
            [-123, 12, 31, 0, 0, 0, 1676496.5],
            [-122, 1, 1, 0, 0, 0, 1676497.5],
            [-1000, 7, 12, 12, 0, 0, 1356001.0],
            [-1000, 2, 29, 0, 0, 0, 1355866.5],
            [-1001, 8, 17, 21, 30, 0, 1355671.4],
            [-4712, 1, 1, 12, 0, 0, 0.0],
        );

        foreach ($data as $t) {
            $dateTime = new AstroDateTime($t[0], $t[1], $t[2], $t[3], $t[4], $t[5]);
            $JD = TimeCalc::dateTime2JulianDay($dateTime);

            $this->assertEquals($t[6], round($JD, 2));
        }
    }

    /**
     * @test
     * TODO Test broke!!!
     */
    public function julianDay2DateTimeTest()
    {
        $data = array(
            [2000, 1, 1, 12, 0, 0, 2451545.0],
            [1999, 1, 1, 0, 0, 0, 2451179.5],
            [1987, 1, 27, 0, 0, 0, 2446822.5],
            [1987, 6, 19, 12, 0, 0, 2446966.0],
            [1988, 1, 27, 0, 0, 0, 2447187.5],
            [1988, 6, 19, 12, 0, 0, 2447332.0],
            [1900, 1, 1, 0, 0, 0, 2415020.5],
            [1600, 1, 1, 0, 0, 0, 2305447.5],
            [1600, 12, 31, 0, 0, 0, 2305812.5],
//            [837, 4, 10, 8, 0, 0, 2026871.83],
            [-123, 12, 31, 0, 0, 0, 1676496.5],
            [-122, 1, 1, 0, 0, 0, 1676497.5],
            [-1000, 7, 12, 12, 0, 0, 1356001.0],
            [-1000, 2, 29, 0, 0, 0, 1355866.5],
//            [-1001, 8, 17, 21, 30, 0, 1355671.4],
            [-4712, 1, 1, 12, 0, 0, 0.0],
        );

        foreach ($data as $t) {
            $dateTime = TimeCalc::julianDay2DateTime($t[6]);

            $this->assertEquals($t[0], $dateTime->year);
            $this->assertEquals($t[1], $dateTime->month);
            $this->assertEquals($t[2], $dateTime->day);
            $this->assertEquals($t[3], $dateTime->hour);
            $this->assertEquals($t[4], $dateTime->minute);
            $this->assertEquals($t[5], $dateTime->second);
        }
    }

    /**
     * @test
     */
    public function julianDay2ModifiedJulianDayTest()
    {
        $JD = 2446895.5;

        $MJD = TimeCalc::julianDay2ModifiedJulianDay($JD);

        $this->assertEquals(46895, $MJD);
    }

    /**
     * @test
     * Meeus 12.a
     */
    public function julianDay2julianCenturiesJ2000Test()
    {
        $JD = 2446895.5;

        $T = TimeCalc::julianDay2julianCenturiesJ2000($JD);

        $this->assertEquals(-0.127296372348, $T);
    }

    /**
     * @test
     * Meeus 12.a
     */
    public function julianCenturiesJ20002JulianDayTest()
    {
        $T = -0.127296372348;

        $JD = TimeCalc::julianCenturiesJ20002JulianDay($T);

        $this->assertEquals(2446895.5, round($JD, 6));
    }

    /**
     * @test
     * Meeus 12.a
     */
    public function julianDay2julianMillenniaJ2000Test()
    {
        $JD = 2446895.5;

        $t = TimeCalc::julianDay2julianMillenniaJ2000($JD);

        $this->assertEquals(-0.0127296372348, $t);
    }

    /**
     * @test
     * Meeus 12.a
     */
    public function julianMillenniaJ20002JulianDayTest()
    {
        $t = -0.0127296372348;

        $JD = TimeCalc::julianMillenniaJ20002JulianDay($t);

        $this->assertEquals(2446895.5, round($JD, 6));
    }

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
     * Meeus 28.a
     */
    public function getEquationOfTimeInDegreesTest()
    {
        $T = -0.072183436;

        $E = TimeCalc::getEquationOfTimeInDegrees($T);

        $this->assertEquals(3.424707, round($E, 6));

        // TODO Use method with higher accuracy (Meeus p.166) 25.9
//        $this->assertEquals(3.427351, round($E, 6));
    }

    /**
     * @test
     * Meeus Table 10.A
     */
    public function getDeltaTTest()
    {
        $array = [
            2050 => 149.2,
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
