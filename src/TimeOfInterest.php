<?php

namespace Andrmoel\AstronomyBundle;

use Andrmoel\AstronomyBundle\Calculations\TimeCalc;
use Andrmoel\AstronomyBundle\Entities\Time;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

class TimeOfInterest
{
    /*
     * CENTURY_SPLIT defines how to handle 2-char years
     * Year is given with XX
     * XX <= CENTURY_SPLIT -> YEAR = CUR_CENTURY_YEAR_PREFIX + XX
     * XX > CENTURY_SPLIT -> YEAR = PAST_CENTURY_YEAR_PREFIX + XX
     *
     * e.g.
     * XX = 10
     * 10 <= 50 -> YEAR = 20 + 10 = 2010
     *
     * another one: XX = 99
     * 99 > 50 -> YEAR = 19 + 99 = 1999
     */
    const CENTURY_SPLIT = 50;
    const PAST_CENTURY_YEAR_PREFIX = '19';
    const CUR_CENTURY_YEAR_PREFIX = '20';

    const DAY_OF_WEEK_SUNDAY = 0;
    const DAY_OF_WEEK_MONDAY = 1;
    const DAY_OF_WEEK_TUESDAY = 2;
    const DAY_OF_WEEK_WEDNESDAY = 3;
    const DAY_OF_WEEK_THURSDAY = 4;
    const DAY_OF_WEEK_FRIDAY = 5;
    const DAY_OF_WEEK_SATURDAY = 6;

    /** @var Time */
    private $time;

    private function __construct(Time $time = null)
    {
        $this->time = $time ? $time : new Time();
    }

    public function __toString(): string
    {
        return $this->time;
    }

    public static function createFromCurrentTime(): self
    {
        return new self();
    }

    public static function create(
        int $year = 0,
        int $month = 0,
        int $day = 0,
        int $hour = 0,
        int $minute = 0,
        int $second = 0
    ): self
    {
        $toi = new self();
        $toi->setTime($year, $month, $day, $hour, $minute, $second);

        return $toi;
    }

    public static function createFromTime(
        int $year = 0,
        int $month = 0,
        int $day = 0,
        int $hour = 0,
        int $minute = 0,
        int $second = 0
    ): self
    {
        $toi = new self();
        $toi->setTime($year, $month, $day, $hour, $minute, $second);

        return $toi;
    }

    public static function createFromDayOfYear(int $year = 0, float $dayOfYear = 0): self
    {
        $toi = new self();
        $toi->setTimeByDayOfYear($year, $dayOfYear);
        return $toi;
    }

    public static function createFromDateTime(\DateTime $dateTime): self
    {
        $toi = new self();
        $toi->setDateTime($dateTime);

        return $toi;
    }

    public static function createFromString(string $dateTimeStr): self
    {
        $toi = new self();
        $toi->setString($dateTimeStr);

        return $toi;
    }

    public static function createFromJulianDay(float $JD): self
    {
        $toi = new self();
        $toi->setJulianDay($JD);

        return $toi;
    }

    public static function createFromJulianCenturiesJ2000(float $T): self
    {
        $JD = TimeCalc::julianCenturiesJ20002julianDay($T);

        $toi = new self();
        $toi->setJulianDay($JD);

        return $toi;
    }

    public function setCurrentTime(): void
    {
        $this->time = new Time();
    }

    public function setTime(
        int $year = 0,
        int $month = 0,
        int $day = 0,
        int $hour = 0,
        int $minute = 0,
        int $second = 0
    ): void
    {
        $this->time = new Time($year, $month, $day, $hour, $minute, $second);
    }

    public function setTimeByDayOfYear(int $year, float $dayOfYear): void
    {
        $this->time = TimeCalc::dayOfYear2time($year, $dayOfYear);
    }

    public function setDateTime(\DateTime $dateTime): void
    {
        $year = (int)$dateTime->format('Y');
        $month = (int)$dateTime->format('m');
        $day = (int)$dateTime->format('d');
        $hour = (int)$dateTime->format('H');
        $minute = (int)$dateTime->format('i');
        $second = (float)$dateTime->format('s');

        $this->time = new Time($year, $month, $day, $hour, $minute, $second);
    }

    public function setString(string $dateTimeStr): void
    {
        $dateTime = new \DateTime($dateTimeStr);

        $this->setDateTime($dateTime);
    }

    public function setJulianDay(float $JD): void
    {
        $this->time = TimeCalc::julianDay2time($JD);
    }

    public function setJulianCenturiesJ2000(float $T): void
    {
        $JD = TimeCalc::julianCenturiesJ20002julianDay($T);

        $this->setJulianDay($JD);
    }

    public function getYear(): int
    {
        return $this->time->year;
    }

    public function getMonth(): int
    {
        return $this->time->month;
    }

    public function getDay(): int
    {
        return $this->time->day;
    }

    public function getHour(): int
    {
        return $this->time->hour;
    }

    public function getMinute(): int
    {
        return $this->time->minute;
    }

    public function getSecond(): int
    {
        return $this->time->second;
    }

    public function getDateTime(): \DateTime
    {
        $dateTime = new \DateTime();
        $dateTime->setDate($this->time->year, $this->time->month, $this->time->day);
        $dateTime->setTime($this->time->hour, $this->time->minute, $this->time->second);

        return $dateTime;
    }

    public function getString(): string
    {
        return $this->time->__toString();
    }

    public function getJulianDay(): float
    {
        $JD = TimeCalc::time2julianDay($this->time);

        return $JD;
    }

    public function getJulianDay0(): float
    {
        $JD = $this->getJulianDay();
        $JD0 = TimeCalc::julianDay2julianDay0($JD);

        return $JD0;
    }

    public function getJulianCenturiesFromJ2000(): float
    {
        $JD = $this->getJulianDay();

        $T = TimeCalc::julianDay2julianCenturiesJ2000($JD);

        return $T;
    }

    public function getJulianMillenniaFromJ2000(): float
    {
        $JD = $this->getJulianDay();

        $t = TimeCalc::julianDay2julianMillenniaJ2000($JD);

        return $t;
    }

    public function getDayOfYear(): int
    {
        $N = TimeCalc::getDayOfYear($this->time);

        return $N;
    }

    public function getDayOfWeek(): int
    {
        $JD = $this->getJulianDay();

        return TimeCalc::getDayOfWeek($JD);
    }

    public function isLeapYear(): bool
    {
        return TimeCalc::isLeapYear($this->time->year);
    }

    public function getGreenwichMeanSiderealTime(): float
    {
        $T = $this->getJulianCenturiesFromJ2000();

        $GMST = TimeCalc::getGreenwichMeanSiderealTime($T);

        return $GMST;
    }

    public function getGreenwichApparentSiderealTime(): float
    {
        $T = $this->getJulianCenturiesFromJ2000();

        $GAST = TimeCalc::getGreenwichApparentSiderealTime($T);

        return $GAST;
    }

    /**
     * TODO Formel
     * @param float $lon
     * @return float
     */
    public function getLocalMeanSiderealTime(float $lon): float
    {
        $lonEast = $lon >= 0 ? $lon : 360 + $lon;

        $gmst = $this->getGreenwichMeanSiderealTime();
        $lmst = $gmst + $lonEast;

        $lmst = AngleUtil::normalizeAngle($lmst);

        return $lmst;
    }

    public function getEquationOfTime(): float
    {
        $T = $this->getJulianCenturiesFromJ2000();

        $E = TimeCalc::getEquationOfTimeInDegrees($T);

        return $E;
    }
}
