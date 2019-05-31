<?php

namespace Andrmoel\AstronomyBundle\Tests;

use Andrmoel\AstronomyBundle\TimeOfInterest;
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
        // TODO ...
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
}
