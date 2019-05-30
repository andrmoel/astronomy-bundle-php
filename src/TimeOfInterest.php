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
        $time = $time ? $time : new Time();

        $this->setTime($time);
    }

    public function __toString(): string
    {
        return $this->time;
    }
    
    public static function createForCurrentTime(): self
    {
        return new self();
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
        $JD = TimeCalc::julianCenturiesJ20002JulianDay($T);

        $toi = new self();
        $toi->setJulianDay($JD);

        return $toi;
    }

    private function setTime(Time $time): void
    {
        $this->time = $time;
    }

    public function setDateTime(\DateTime $dateTime): void
    {
        $year = (int)$dateTime->format('Y');
        $month = (int)$dateTime->format('m');
        $day = (int)$dateTime->format('d');
        $hour = (int)$dateTime->format('H');
        $minute = (int)$dateTime->format('i');
        $second = (float)$dateTime->format('s');

        $time = new Time($year, $month, $day, $hour, $minute, $second);

        $this->setTime($time);
    }

    public function setString(string $dateTimeStr): void
    {
        $dateTime = new \DateTime($dateTimeStr);

        $this->setDateTime($dateTime);
    }

    public function setJulianDay(float $JD): void
    {
        $dateTime = TimeCalc::julianDay2DateTime($JD);

        $this->setTime($dateTime);
    }

    public function setJulianCenturiesJ2000(float $T): void
    {
        $JD = TimeCalc::julianCenturiesJ20002JulianDay($T);

        $this->setJulianDay($JD);
    }

    public function getDateTime(): \DateTime
    {
        $dateTime = new \DateTime();
        $dateTime->setDate($this->time->year, $this->time->month, $this->time->day);
        $dateTime->setTime($this->time->hour, $this->time->minute, $this->time->second);

        return $dateTime;
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

    public function getJulianDay(): float
    {
        $JD = TimeCalc::dateTime2JulianDay($this->time);

        return $JD;
    }

    public function getJulianDay0(): float
    {
        $JD = $this->getJulianDay();
        $JD0 = TimeCalc::julianDay2JulianDay0($JD);

        return $JD0;
    }

    // TODO ... use TimeCalc
    public function getDayOfYear(): int
    {
        $K = $this->isLeapYear($this->time->year) ? 1 : 2;
        $M = $this->time->month;
        $D = $this->time->day;

        // Meeus 7.f
        $N = (int)((275 * $M) / 9) - $K * (int)(($M + 9) / 12) + $D - 30;

        return $N;
    }

    /**
     * Meeus 7
     * @param int $year
     * @param int $dayOfYear
     * @param int $hour
     * @param int $minute
     * @param int $second
     */
    public function setTimeByDayOfYear(
        int $year = 0,
        int $dayOfYear = 0,
        int $hour = 0,
        int $minute = 0,
        int $second = 0
    ): void {
        $K = $this->isLeapYear($year) ? 1 : 2;
        $month = $dayOfYear < 32 ? 1 : (int)((9 * ($K + $dayOfYear)) / 275 + 0.98);
        $day = $dayOfYear - (int)((275 * $month) / 9) + $K * (int)(($month + 9) / 12) + 30;

        $this->time = new Time($year, $month, $day, $hour, $minute, $second);
    }

    public function isLeapYear(int $year): bool
    {
        return TimeCalc::isLeapYear($year);
    }

    public function getDayOfWeek(): int
    {
        $JD = $this->getJulianDay();

        return TimeCalc::getDayOfWeek($JD);
    }


    // TODO ------------------------------------


    /**
     * @param $epoch
     * @deprecated
     */
    private function setTleEpoch($epoch): void
    {
        $parts = explode('.', $epoch);
        $year = substr($parts[0], 0, 2);
        $dayOfYear = substr($parts[0], 2, strlen($parts[0]) - 2);

        // Get full year from 2-char year
        $prefix = $year <= self::CENTURY_SPLIT ? self::CUR_CENTURY_YEAR_PREFIX : self::PAST_CENTURY_YEAR_PREFIX;
        $year = intval($prefix . $year);

        // Get TimeOfInterest for day of month
        $toi = new self();
        $toi->setTimeByDayOfYear($year, $dayOfYear);

        // Calculate hours, minutes and seconds
        $decimalPart = doubleval('0.' . $parts[1]);
        $hour = ($decimalPart - (int)$decimalPart) * 24;
        $minute = ($hour - (int)$hour) * 60;
        $second = ($minute - (int)$minute) * 60;

        $this->year = $year;
        $this->month = $toi->getMonth();
        $this->day = $toi->getDay();
        $this->hour = (int)$hour;
        $this->minute = (int)$minute;
        $this->second = (int)$second;
    }

    public function getJulianCenturiesFromJ2000(): float
    {
        $JD = $this->getJulianDay();

        $T = TimeCalc::julianDay2JulianCenturiesJ2000($JD);

        return $T;
    }

    public function getJulianMillenniaFromJ2000(): float
    {
        $T = $this->getJulianCenturiesFromJ2000();
        $t = $T / 10;

        return $t;
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
