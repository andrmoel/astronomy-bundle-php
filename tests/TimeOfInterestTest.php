<?php

namespace Andrmoel\AstronomyBundle\Tests;

use Andrmoel\AstronomyBundle\TimeOfInterest;
use PHPUnit\Framework\TestCase;

class TimeOfInterestTest extends TestCase
{
    public function testGetDateTimeFromConstructor()
    {
        $dateTimeNow = new \DateTime();
        $dateTimeNow->setTime(0, 0, 0);

        $toi = new TimeOfInterest($dateTimeNow);
        $dateTime = $toi->getDateTime();

        $this->assertEquals($dateTimeNow, $dateTime);
    }

    public function testGetDateTimeFromSetDateTime()
    {
        $dateTimeNow = new \DateTime();
        $dateTimeNow->setTime(0, 0, 0);

        $toi = new TimeOfInterest();
        $toi->setDateTime($dateTimeNow);
        $dateTime = $toi->getDateTime();

        $this->assertEquals($dateTimeNow, $dateTime);
    }

    /**
     * Meeus 7.a
     */
    public function testGetJulianDay()
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
            $toi = new TimeOfInterest();
            $toi->setTime($t[0], $t[1], $t[2], $t[3], $t[4], $t[5]);
            $JD = $toi->getJulianDay();

            $this->assertEquals($t[6], round($JD, 2));
        }
    }

    public function testGetJulianDay0()
    {
        $toi = new TimeOfInterest(new \DateTime('1957-10-04 19:28:34'));
        $JD0 = $toi->getJulianDay0();

        $this->assertEquals(2436115.5, round($JD0, 1));
    }

    public function testModifiedJulianDay0()
    {
        $toi = new TimeOfInterest(new \DateTime('1858-11-17 00:00:00'));
        $MJD = $toi->getModifiedJulianDay();

        $this->assertEquals(0.0, round($MJD, 1));
    }

    /**
     * Meeus 7.c
     */
    public function testSetJulianDay()
    {
        $toi = new TimeOfInterest();
        $toi->setJulianDay(2436116.31);

        $this->assertEquals(1957, $toi->getYear());
        $this->assertEquals(10, $toi->getMonth());
        $this->assertEquals(4, $toi->getDay());
        $this->assertEquals(19, $toi->getHour());
        $this->assertEquals(26, $toi->getMinute());
        $this->assertEquals(24, $toi->getSecond());
    }

    /**
     * Meeus 7.e
     */
    public function testGetDayOfWeek()
    {
        $toi = new TimeOfInterest(new \DateTime('1954-06-30 00:00:00'));
        $DOW = $toi->getDayOfWeek();

        $this->assertEquals(TimeOfInterest::DAY_OF_WEEK_WEDNESDAY, $DOW);
    }

    public function testGetDayOfYear()
    {
        // Meeus 7.f
        $toi = new TimeOfInterest(new \DateTime('1978-11-14 00:00:00'));
        $DOY = $toi->getDayOfYear();

        $this->assertEquals(318, $DOY);

        // Meeus 7.g
        $toi = new TimeOfInterest(new \DateTime('1988-04-22 00:00:00'));
        $DOY = $toi->getDayOfYear();

        $this->assertEquals(113, $DOY);
    }

    public function testSetTimeByDayOfYear()
    {
        // Meeus 7.f
        $toi = new TimeOfInterest();
        $toi->setTimeByDayOfYear(1978, 318, 0, 0, 0);

        $dateTime = $toi->getDateTime();

        $this->assertEquals('1978-11-14', $dateTime->format('Y-m-d'));

        // Meeus 7.g
        $toi = new TimeOfInterest();
        $toi->setTimeByDayOfYear(1988, 113, 0, 0, 0);

        $dateTime = $toi->getDateTime();

        $this->assertEquals('1988-04-22', $dateTime->format('Y-m-d'));
    }

    /**
     * Meeus 12.a
     */
    public function testGetGreenwichMeanSiderealTime()
    {
        $toi = new TimeOfInterest(new \DateTime('1987-04-10 00:00:00'));
        $t0 = $toi->getGreenwichMeanSiderealTime();

        $this->assertEquals(197.6932, round($t0, 5));
    }

    /**
     * Meeus 12.a
     */
    public function testGetGreenwichApparentMeanSiderealTime()
    {
        $toi = new TimeOfInterest(new \DateTime('1987-04-10 00:00:00'));
        $t = $toi->getGreenwichApparentSiderealTime();

        $this->assertEquals(197.69223, round($t, 5));
    }
}
