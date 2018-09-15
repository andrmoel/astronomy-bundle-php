<?php

namespace Andrmoel\AstronomyBundle;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Earth;

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

    // Time parameter
    private $year = 0;
    private $month = 0;
    private $day = 0;
    private $hour = 0;
    private $minute = 0;
    private $second = 0;

    public function __construct(\DateTime $dateTime = null)
    {
        $dateTime = $dateTime ? $dateTime : new \DateTime();

        $this->year = (int)$dateTime->format('Y');
        $this->month = (int)$dateTime->format('m');
        $this->day = (int)$dateTime->format('d');
        $this->hour = (int)$dateTime->format('H');
        $this->minute = (int)$dateTime->format('i');
        $this->second = (float)$dateTime->format('s');
    }

    public function setDateTime(\DateTime $dateTime): void
    {
        $this->year = (int)$dateTime->format('Y');
        $this->month = (int)$dateTime->format('m');
        $this->day = (int)$dateTime->format('d');
        $this->hour = (int)$dateTime->format('H');
        $this->minute = (int)$dateTime->format('i');
        $this->second = (int)$dateTime->format('s');
    }

    public function getDateTime(): \DateTime
    {
        $dateTime = new \DateTime();
        $dateTime->setDate($this->year, $this->month, $this->day);
        $dateTime->setTime($this->hour, $this->minute, $this->second);

        return $dateTime;
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
        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
        $this->hour = $hour;
        $this->minute = $minute;
        $this->second = $second;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getMonth(): int
    {
        return $this->month;
    }

    public function getDay(): int
    {
        return $this->day;
    }

    public function getHour(): int
    {
        return $this->hour;
    }

    public function getMinute(): int
    {
        return $this->minute;
    }

    public function getSecond(): int
    {
        return $this->second;
    }

    public function getJulianDay(bool $jd0 = false): float
    {
        $tmpYear = floatval($this->year . '.' . $this->getDayOfYear());

        if ($this->month > 2) {
            $Y = $this->year;
            $M = $this->month;
        } else {
            $Y = $this->year - 1;
            $M = $this->month + 12;
        }

        $D = $this->day;
        $H = $jd0 ? 0 : $this->hour / 24 + $this->minute / 1440 + $this->second / 86400;

        if ($tmpYear >= 1582.288) { // YYYY-MM-DD >= 1582-10-15
            $A = (int)($Y / 100);
            $B = 2 - $A + (int)($A / 4);
        } elseif ($tmpYear <= 1582.277) { // YY-MM-DD <= 1582-10-04
            $B = 0;
        } else {
            throw new \Exception('Date between 1582-10-04 and 1582-10-15 is not defined.');
        }

        // Meeus 7.1
        $JD = (int)(365.25 * ($Y + 4716)) + (int)(30.6001 * ($M + 1)) + $D + $H + $B - 1524.5;

        return $JD;
    }

    public function getJulianDay0(): float
    {
        $JD0 = $this->getJulianDay(true);

        return $JD0;
    }

    public function getModifiedJulianDay(): float
    {
        $JD = $this->getJulianDay();
        $MJD = $JD - 2400000.5;

        return $MJD;
    }

    public function setJulianDay(float $JD): void
    {
        $JD = $JD + 0.5;
        $Z = (int)$JD;
        $F = $JD - $Z;

        $A = $Z;
        if ($Z < 2299161) {
            $A = $Z;
        } elseif ($Z >= 2291161) {
            $a = (int)(($Z - 1867216.25) / 36524.25);
            $A = $Z + 1 + $a - (int)($a / 4);
        }

        $B = $A + 1524;
        $C = (int)(($B - 122.1) / 365.25);
        $D = (int)(365.25 * $C);
        $E = (int)(($B - $D) / 30.6001);

        $dayOfMonth = $B - $D - (int)(30.6001 * $E) + $F;
        $month = $E < 14 ? $E - 1 : $E - 13;
        $year = $month > 2 ? $C - 4716 : $C - 4715;
        $hour = ($dayOfMonth - (int)$dayOfMonth) * 24;
        $minute = ($hour - (int)$hour) * 60;
        $second = ($minute - (int)$minute) * 60;

        $this->year = $year;
        $this->month = (int)$month;
        $this->day = (int)$dayOfMonth;
        $this->hour = (int)$hour;
        $this->minute = (int)$minute;
        $this->second = (int)$second;
    }

    public function isLeapYear(int $year): bool
    {
        if ($year / 4 != (int)($year / 4)) {
            return false;
        } elseif ($year / 100 != (int)($year / 100)) {
            return true;
        } elseif ($year / 400 != (int)($year / 400)) {
            return false;
        } else {
            return true;
        }
    }

    public function getDayOfWeek(): int
    {
        $JD = $this->getJulianDay();

        // Meeus 7.e
        $DOW = ($JD + 1.5) % 7;

        return $DOW;
    }

    public function getDayOfYear(): int
    {
        $K = $this->isLeapYear($this->year) ? 1 : 2;
        $M = $this->month;
        $D = $this->day;

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
    ): void
    {
        $K = $this->isLeapYear($year) ? 1 : 2;
        $month = $dayOfYear < 32 ? 1 : (int)((9 * ($K + $dayOfYear)) / 275 + 0.98);
        $day = $dayOfYear - (int)((275 * $month) / 9) + $K * (int)(($month + 9) / 12) + 30;

        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
        $this->hour = $hour;
        $this->minute = $minute;
        $this->second = $second;
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

    public function getJulianCenturiesSinceJ2000(): float
    {
        $jd = $this->getJulianDay();
        $T = ($jd - 2451545.0) / 36525.0;

        return $T;
    }

    public function getUniversalTime(): int
    {
        // TODO write
        $ut = 0;
        return $ut;
    }

    public function getGreenwichMeanSiderealTime(): float
    {
        $JD = $this->getJulianDay();
        $T = $this->getJulianCenturiesSinceJ2000();

        // Meeus 12.3
//        $t0 = 100.46061837
//            + 36000.770053608 * $T
//            + 0.000387933 * pow($T, 2)
//            - pow($T, 3) / 38710000;

        // Meeus 12.4
        $t0 = 280.46061837
            + 360.98564736629 * ($JD - 2451545)
            + 0.000387933 * pow($T, 2)
            + pow($T, 3) / 38710000;
        $t0 = Util::normalizeAngle($t0);

        return $t0;
    }

    // TODO Benötigt?
    public function getGreenwichMeanSiderealTimeInHours(): float
    {
        $gmst = $this->getGreenwichMeanSiderealTime();
        $t0 = $gmst / 15;

        return $t0;
    }

    public function getApparentGreenwichMeanSiderealTime(): float
    {
        $earth = new Earth($this);

        $t0 = $this->getGreenwichMeanSiderealTime();
        $p = $earth->getNutation();
        $e = deg2rad($earth->getTrueObliquityOfEcliptic());

        // Meeus 12
        $gmst = $t0 + $p * cos($e);

        return $gmst;
    }

    /**
     * TODO Formel
     * @param float $lon
     * @return float
     */
    public function getLocalMeanSiderealTime(float $lon): float
    {
        $lonEast = $lon >= 0 ? $lon : 360 + $lon;

        $gmst = $this->getGreenwichMeanSiderealTime(false);
        $lmst = $gmst + $lonEast;

        $lmst = Util::normalizeAngle($lmst);

        return $lmst;
    }

    // TODO Benötigt???
    public function getLocalMeanSiderealTimeInHours(float $lon): float
    {
        $lmst = $this->getLocalMeanSiderealTime($lon);
        $lmst = $lmst / 15;

        return $lmst;
    }
}
