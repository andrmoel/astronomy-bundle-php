<?php

namespace Andrmoel\AstronomyBundle\Tests;

use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;
use PHPUnit\Framework\TestCase;

class TimeOfInterestTest extends TestCase
{
    /**
     * @test
     */
    public function createFromCurrentTimeTest()
    {
        $toi = TimeOfInterest::createFromCurrentTime();

        $dateTime = new \DateTime();
        $this->assertEquals($dateTime->format('Y-m-d H:i:s'), $toi);
    }

    /**
     * @test
     */
    public function createFromTimeTest()
    {
        $toi = TimeOfInterest::createFromTime(2017, 1, 1, 12, 30, 0);

        $this->assertEquals('2017-01-01 12:30:00', $toi);
    }

    /**
     * @test
     */
    public function createFromDayOfYearTest()
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
            $toi = TimeOfInterest::createFromDayOfYear($data[0], $data[1]);
            $this->assertEquals($data[2], $toi);
        }
    }

    /**
     * @test
     */
    public function createFromDateTimeTest()
    {
        $dateTime = new \DateTime('2017-01-01 12:30:00');
        $toi = TimeOfInterest::createFromDateTime($dateTime);

        $this->assertEquals('2017-01-01 12:30:00', $toi);
    }

    /**
     * @test
     */
    public function createFromStringTest()
    {
        $toi = TimeOfInterest::createFromString('2017-01-01 12:30:00');

        $this->assertEquals('2017-01-01 12:30:00', $toi);
    }

    /**
     * @test
     */
    public function createFromJulianDayTest()
    {
        $toi = TimeOfInterest::createFromJulianDay(2457755.0208334);

        $this->assertEquals('2017-01-01 12:30:00', $toi);
    }

    /**
     * @test
     */
    public function createFromJulianCenturiesJ2000Test()
    {
        $toi = TimeOfInterest::createFromJulianCenturiesJ2000(0.17002110426649);

        $this->assertEquals('2017-01-01 12:30:00', $toi);
    }

    /**
     * @test
     */
    public function setCurrentTimeTest()
    {
        $toi = TimeOfInterest::createFromString('1999-01-01 00:00:00');
        $toi->setCurrentTime();

        $dateTime = new \DateTime();
        $this->assertEquals($dateTime->format('Y-m-d H:i:s'), $toi);
    }

    /**
     * @test
     */
    public function setTimeTest()
    {
        $toi = TimeOfInterest::createFromString('1999-01-01 00:00:00');
        $toi->setTime(2017, 1, 1, 12, 30, 0);

        $this->assertEquals('2017-01-01 12:30:00', $toi);
    }

    /**
     * @test
     */
    public function setTimeByDayOfYearTest()
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

        $toi = TimeOfInterest::createFromString('1999-01-01 00:00:00');

        foreach ($dataArray as $data) {
            $toi->setTimeByDayOfYear($data[0], $data[1]);
            $this->assertEquals($data[2], $toi);
        }
    }

    /**
     * @test
     */
    public function setDateTimeTest()
    {
        $toi = TimeOfInterest::createFromString('1999-01-01 00:00:00');

        $dateTime = new \DateTime('2017-01-01 12:30:00');
        $toi->setDateTime($dateTime);

        $this->assertEquals('2017-01-01 12:30:00', $toi);
    }

    /**
     * @test
     */
    public function setStringTest()
    {
        $toi = TimeOfInterest::createFromString('1999-01-01 00:00:00');
        $toi->setString('2017-01-01 12:30:00');

        $this->assertEquals('2017-01-01 12:30:00', $toi);
    }

    /**
     * @test
     */
    public function setJulianDayTest()
    {
        $toi = TimeOfInterest::createFromString('1999-01-01 00:00:00');
        $toi->setJulianDay(2457755.0208334);

        $this->assertEquals('2017-01-01 12:30:00', $toi);
    }

    /**
     * @test
     */
    public function setJulianCenturiesJ2000Test()
    {
        $toi = TimeOfInterest::createFromString('1999-01-01 00:00:00');
        $toi->setJulianCenturiesJ2000(0.17002110426649);

        $this->assertEquals('2017-01-01 12:30:00', $toi);
    }

    /**
     * @test
     */
    public function getterTest()
    {
        $toi = TimeOfInterest::create(2000, 12, 5, 13, 56, 45);

        $this->assertEquals(2000, $toi->getYear());
        $this->assertEquals(12, $toi->getMonth());
        $this->assertEquals(5, $toi->getDay());
        $this->assertEquals(13, $toi->getHour());
        $this->assertEquals(56, $toi->getMinute());
        $this->assertEquals(45, $toi->getSecond());
        $this->assertEquals(new \DateTime('2000-12-05 13:56:45'), $toi->getDateTime());
        $this->assertEquals('2000-12-05 13:56:45', $toi->getString());
        $this->assertEquals(2451884.08108, round($toi->getJulianDay(), 5));
        $this->assertEquals(2451883.5, round($toi->getJulianDay0(), 5));
        $this->assertEquals(0.00928353, round($toi->getJulianCenturiesFromJ2000(), 8));
        $this->assertEquals(0.00092835, round($toi->getJulianMillenniaFromJ2000(), 8));
        $this->assertEquals(340, $toi->getDayOfYear());
        $this->assertEquals(TimeOfInterest::DAY_OF_WEEK_TUESDAY, $toi->getDayOfWeek());
        $this->assertTrue($toi->isLeapYear());
    }

    /**
     * @test
     */
    public function getGreenwichMeanSiderealTimeTest()
    {
        $toi = TimeOfInterest::createFromString('1987-04-10 00:00:00');

        $GMST = $toi->getGreenwichMeanSiderealTime();
        $GMST = AngleUtil::dec2time($GMST);

        $this->assertEquals('13h10m46.367s', $GMST);

        $toi = TimeOfInterest::createFromString('1987-04-10 19:21:00');

        $GMST = $toi->getGreenwichMeanSiderealTime();
        $GMST = AngleUtil::dec2time($GMST);

        $this->assertEquals('8h34m57.09s', $GMST);
    }

    /**
     * @test
     */
    public function getGreenwichApparentSiderealTime()
    {
        $toi = TimeOfInterest::createFromString('1987-04-10 00:00:00');

        $GAST = $toi->getGreenwichApparentSiderealTime();
        $GAST = AngleUtil::dec2time($GAST);

        $this->assertEquals('13h10m46.135s', $GAST);
    }

    /**
     * @test
     */
//    public function getLocalMeanSiderealTime()
//    {
//        // TODO
//    }

    /**
     * @test
     */
    public function getEquationOfTime()
    {
        $toi = TimeOfInterest::createFromString('1992-10-13 00.00:00');

        $E = $toi->getEquationOfTime();

        $this->assertEquals(3.424707, round($E, 6));

        // TODO Use method with higher accuracy (Meeus p.166) 25.9
//        $this->assertEquals(3.427351, round($E, 6));
    }
}
