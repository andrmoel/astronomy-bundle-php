<?php

namespace Andrmoel\AstronomyBundle;

use Andrmoel\AstronomyBundle\Calculations\TimeCalc;
use Andrmoel\AstronomyBundle\Entities\AstroDateTime;
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

    /** @var AstroDateTime */
    private $astroDateTime;

    public function __construct(AstroDateTime $astroDateTime = null)
    {
        $astroDateTime = $astroDateTime ? $astroDateTime : new AstroDateTime();

        $this->setAstroDateTime($astroDateTime);
    }

    public function __toString(): string
    {
        return $this->astroDateTime;
    }

    public static function createFromDateTime(\DateTime $dateTime): self
    {
        $toi = new TimeOfInterest();
        $toi->setDateTime($dateTime);

        return $toi;
    }

    public static function createFromString(string $dateTimeStr): self
    {
        $toi = new TimeOfInterest();
        $toi->setString($dateTimeStr);

        return $toi;
    }

    public static function createFromJulianDay(float $JD): self
    {
        $toi = new TimeOfInterest();
        $toi->setJulianDay($JD);

        return $toi;
    }

    public static function createFromJulianCenturiesJ2000(float $T): self
    {
        $JD = TimeCalc::julianCenturiesJ20002JulianDay($T);

        $toi = new TimeOfInterest();
        $toi->setJulianDay($JD);

        return $toi;
    }

    private function setAstroDateTime(AstroDateTime $astroDateTime): void
    {
        $this->astroDateTime = $astroDateTime;
    }

    public function setDateTime(\DateTime $dateTime): void
    {
        $astroDateTime = AstroDateTime::createFromDateTime($dateTime);

        $this->setAstroDateTime($astroDateTime);
    }

    public function setString(string $dateTimeStr): void
    {
        $dateTime = new \DateTime($dateTimeStr);
        $astroDateTime = AstroDateTime::createFromDateTime($dateTime);

        $this->setAstroDateTime($astroDateTime);
    }

    public function setJulianDay(float $JD): void
    {
        $dateTime = TimeCalc::julianDay2DateTime($JD);

        $this->setAstroDateTime($dateTime);
    }

    public function setJulianCenturiesJ2000(float $T): void
    {
        $JD = TimeCalc::julianCenturiesJ20002JulianDay($T);

        $this->setJulianDay($JD);
    }

    public function getDateTime(): \DateTime
    {
        $dateTime = new \DateTime();
        $dateTime->setDate($this->astroDateTime->year, $this->astroDateTime->month, $this->astroDateTime->day);
        $dateTime->setTime($this->astroDateTime->hour, $this->astroDateTime->minute, $this->astroDateTime->second);

        return $dateTime;
    }

    public function setTime(
        int $year = 0,
        int $month = 0,
        int $day = 0,
        int $hour = 0,
        int $minute = 0,
        int $second = 0
    ): void {
        $this->astroDateTime = new AstroDateTime($year, $month, $day, $hour, $minute, $second);
    }

    public function getYear(): int
    {
        return $this->astroDateTime->year;
    }

    public function getMonth(): int
    {
        return $this->astroDateTime->month;
    }

    public function getDay(): int
    {
        return $this->astroDateTime->day;
    }

    public function getHour(): int
    {
        return $this->astroDateTime->hour;
    }

    public function getMinute(): int
    {
        return $this->astroDateTime->minute;
    }

    public function getSecond(): int
    {
        return $this->astroDateTime->second;
    }

    public function getJulianDay(): float
    {
        $JD = TimeCalc::dateTime2JulianDay($this->astroDateTime);

        return $JD;
    }

    public function getJulianDay0(): float
    {
        $JD = $this->getJulianDay();
        $JD0 = TimeCalc::julianDay2JulianDay0($JD);

        return $JD0;
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

    // TODO ... use TimeCalc
    public function getDayOfYear(): int
    {
        $K = $this->isLeapYear($this->astroDateTime->year) ? 1 : 2;
        $M = $this->astroDateTime->month;
        $D = $this->astroDateTime->day;

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

        $this->astroDateTime = new AstroDateTime($year, $month, $day, $hour, $minute, $second);
    }


    // TODO ------------------------------------


    public function setTleEpoch($epoch): void // TODO type
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
