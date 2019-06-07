<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Calculations\TimeCalc;
use Andrmoel\AstronomyBundle\Entities\Time;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;
use PHPUnit\Framework\TestCase;

class TimeCalcTest extends TestCase
{
    /**
     * @test
     */
    public function julianDay2julianDay0Test()
    {
        $data = [
            [2451545.0, 2451544.5],
            [2447187.5, 2447187.5],
            [2026871.83, 2026871.5],
            [2026871.3, 2026870.5],
            [1, 0.5],
        ];

        foreach ($data as $t) {
            $JD0 = TimeCalc::julianDay2julianDay0($t[0]);

            $this->assertEquals($t[1], $JD0);
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
     * Meeus 7a
     */
    public function time2julianDayTest()
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
            [837, 4, 10, 8, 0, 0, 2026871.833333],
            [-123, 12, 31, 0, 0, 0, 1676496.5],
            [-122, 1, 1, 0, 0, 0, 1676497.5],
            [-1000, 7, 12, 12, 0, 0, 1356001.0],
            [-1000, 2, 29, 0, 0, 0, 1355866.5],
            [-1001, 8, 17, 21, 30, 0, 1355671.395833],
            [-4712, 1, 1, 12, 0, 0, 0.0],
        );

        foreach ($data as $t) {
            $dateTime = new Time($t[0], $t[1], $t[2], $t[3], $t[4], $t[5]);
            $JD = TimeCalc::time2julianDay($dateTime);

            $this->assertEquals($t[6], round($JD, 6));
        }
    }

    /**
     * @test
     */
    public function julianDay2timeTest()
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
            [837, 4, 10, 8, 0, 0, 2026871.83334],
            [-123, 12, 31, 0, 0, 0, 1676496.5],
            [-122, 1, 1, 0, 0, 0, 1676497.5],
            [-1000, 7, 12, 12, 0, 0, 1356001.0],
            [-1000, 2, 29, 0, 0, 0, 1355866.5],
            [-1001, 8, 17, 21, 30, 0, 1355671.395834],
            [-4712, 1, 1, 12, 0, 0, 0.0],
        );

        foreach ($data as $t) {
            $dateTime = TimeCalc::julianDay2time($t[6]);

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
    public function julianCenturiesJ20002julianDayTest()
    {
        $T = -0.127296372348;

        $JD = TimeCalc::julianCenturiesJ20002julianDay($T);

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
    public function julianMillenniaJ20002julianDayTest()
    {
        $t = -0.0127296372348;

        $JD = TimeCalc::julianMillenniaJ20002julianDay($t);

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

    /**
     * @test
     * Meeus 7f
     */
    public function dayOfYear2timeTest()
    {
        $dataArray = [
            [2000, 1, '2000-01-01 00:00:00'],
            [2000, 1.5, '2000-01-01 12:00:00'],
            [2000, 1.87654, '2000-01-01 21:02:13'],
            [2000, 10, '2000-01-10 00:00:00'],
            [2000, 10.5, '2000-01-10 12:00:00'],
            [2000, 10.87654, '2000-01-10 21:02:13'],
            [2011, 100, '2011-04-10 00:00:00'],
            [2011, 100.5, '2011-04-10 12:00:00'],
            [2011, 100.87654, '2011-04-10 21:02:13'],
            [2011, 321, '2011-11-17 00:00:00'],
            [2011, 321.5, '2011-11-17 12:00:00'],
            [2011, 321.87654, '2011-11-17 21:02:13'],
        ];
        foreach ($dataArray as $data) {
            $time = TimeCalc::dayOfYear2time($data[0], $data[1]);
            $this->assertEquals($data[2], $time);
        }
    }

    /**
     * @test
     * Meeus 7f
     */
    public function getDayOfYearTest()
    {
        $dateTime = new \DateTime('2001-01-01');

        for ($expectedDayOfYear = 1; $expectedDayOfYear <= 365; $expectedDayOfYear++) {
            $time = new Time(
                (int)$dateTime->format('Y'),
                (int)$dateTime->format('m'),
                (int)$dateTime->format('d')
            );
            $dayOfYear = TimeCalc::getDayOfYear($time);

            $this->assertEquals($expectedDayOfYear, $dayOfYear);

            $dateTime->add(new \DateInterval('P1D'));
        }
    }

    /**
     * @test
     * Meeus 7.e
     */
    public function getDayOfWeek()
    {
        $days = [
            [2434923.5, TimeOfInterest::DAY_OF_WEEK_WEDNESDAY],
            [2434923.8, TimeOfInterest::DAY_OF_WEEK_WEDNESDAY],
            [2458634.5, TimeOfInterest::DAY_OF_WEEK_FRIDAY],
        ];

        foreach ($days as $expectedDayOfWeek) {
            $dayOfWeek = TimeCalc::getDayOfWeek($expectedDayOfWeek[0]);

            $this->assertEquals($expectedDayOfWeek[1], $dayOfWeek);
        }
    }

    /**
     * @test
     */
    public function isLeapYearTest()
    {
        $years = [
            1700 => false,
            1800 => false,
            1900 => false,
            2000 => true,
            2001 => false,
            2002 => false,
            2003 => false,
            2004 => true,
            2005 => false,
            2006 => false,
            2007 => false,
            2008 => true,
            2012 => true,
            2016 => true,
            2020 => true,
            2024 => true,
            2028 => true,
            2032 => true,
            2036 => true,
            2040 => true,
            2044 => true,
            2048 => true,
        ];

        foreach ($years as $year => $expectedValue) {
            $isLeapYear = TimeCalc::isLeapYear($year);

            $this->assertEquals($expectedValue, $isLeapYear);
        }
    }

    /**
     * @test
     */
    public static function yearTwoDigits2yearTest()
    {
        /*
         * 2000: 00 -> 2000
         * 2010: 00 -> 2000
         * 2090: 00 -> 2000
         * 2100: 00 -> 2100
         * 2000: 99 -> 1999
         * 2010: 99 -> 1999
         * 2090: 99 -> 1999
         * 2100: 99 -> 2099
         */
//        $twoYearDigits
        // TODO Write test
    }
}
