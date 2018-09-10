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

    // Time parameter
    private $year = 0;
    private $month = 0;
    private $day = 0;
    private $hour = 0;
    private $minute = 0;
    private $second = 0;


    public function __construct()
    {
        $tmpTime = date('Y,m,d,H,i,s', time());
        $tmpArr = explode(',', $tmpTime);

        $this->year = (int)$tmpArr[0];
        $this->month = (int)$tmpArr[1];
        $this->day = (int)$tmpArr[2];
        $this->hour = (int)$tmpArr[3];
        $this->minute = (int)$tmpArr[4];
        $this->second = (int)$tmpArr[5];
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


    public function setTimeByDayOfYear(
        int $year = 0,
        int $dayOfYear = 0,
        int $hour = 0,
        int $minute = 0,
        int $second = 0
    ): void
    {
        $k = $this->isLeapYear($this->year) ? 1 : 2;
        $month = $dayOfYear < 32 ? 1 : (int)((9 * ($k + $dayOfYear)) / 275 + 0.98);
        $day = $dayOfYear - (int)((275 * $month) / 9) + $k * (int)(($month + 9) / 12) + 30;

        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
        $this->hour = $hour;
        $this->minute = $minute;
        $this->second = $second;
    }


    public function setDateTime(\DateTime $dateTime): void
    {
        $this->year = $dateTime->format('Y');
        $this->month = $dateTime->format('m');
        $this->day = $dateTime->format('d');
        $this->hour = $dateTime->format('H');
        $this->minute = $dateTime->format('i');
        $this->second = $dateTime->format('s');
    }


    public function setJulianDay(float $jd): void
    {
        $jd = $jd + 0.5;
        $z = (int)$jd;
        $f = $jd - $z;

        $a = $z;
        if ($z < 2299161) {
            $a = $z;
        } elseif ($z >= 2291161) {
            $a = (int)(($z - 1867216.25) / 36524.25);
            $a = $z + 1 + $a - (int)($a / 4);
        }

        $b = $a + 1524;
        $c = (int)(($b - 122.1) / 365.25);
        $d = (int)(365.25 * $c);
        $e = (int)(($b - $d) / 30.6001);

        $dayOfMonth = $b - $d - (int)(30.6001 * $e) + $f;
        $month = $e < 14 ? $e - 1 : $e - 13;
        $year = $month > 2 ? $c - 4716 : $c - 4715;
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


    public function setUnixTime(int $unixTime): void
    {
        $tmpTime = date('Y,m,d,H,i,s', $unixTime);
        $tmpArr = explode(',', $tmpTime);

        $this->year = (int)$tmpArr[0];
        $this->month = (int)$tmpArr[1];
        $this->day = (int)$tmpArr[2];
        $this->hour = (int)$tmpArr[3];
        $this->minute = (int)$tmpArr[4];
        $this->second = (int)$tmpArr[5];
    }


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


    /**
     * Get minute
     * @return int
     */
    public function getMinute(): int
    {
        return $this->minute;
    }


    public function getSecond(): int
    {
        return $this->second;
    }


    public function getDateTime(): \DateTime
    {
        $dateTime = new \DateTime();
        $dateTime->setDate($this->year, $this->month, $this->day);
        $dateTime->setTime($this->hour, $this->minute, $this->second);

        return $dateTime;
    }


    public function getTimeString(): string
    {
        $timeStr = $this->year . '-';
        $timeStr .= substr('0' . $this->month, -2) . '-';
        $timeStr .= substr('0' . $this->day, -2) . ' ';
        $timeStr .= substr('0' . $this->hour, -2) . ':';
        $timeStr .= substr('0' . $this->minute, -2) . ':';
        $timeStr .= substr('0' . $this->second, -2);

        return $timeStr;
    }


    public function getFormattedTimeString(string $format): string
    {
        $Y = $this->year . '-';
        $m = substr('0' . $this->month, -2);
        $d = substr('0' . $this->day, -2);
        $H = substr('0' . $this->hour, -2);
        $i = substr('0' . $this->minute, -2);
        $s = substr('0' . $this->second, -2);

        $format = str_replace('Y', $Y, $format);
        $format = str_replace('m', $m, $format);
        $format = str_replace('d', $d, $format);
        $format = str_replace('H', $H, $format);
        $format = str_replace('i', $i, $format);
        $format = str_replace('s', $s, $format);

        return $format;
    }


    public function getJulianDay(bool $jd0 = false): float  // TODO float or int
    {
        $tmpYear = floatval($this->year . '.' . $this->getDayOfYear());

        if ($this->month > 2) {
            $y = $this->year;
            $m = $this->month;
        } else {
            $y = $this->year - 1;
            $m = $this->month + 12;
        }

        $d = $this->day;
        $h = $jd0 ? 0 : $this->hour / 24 + $this->minute / 1440 + $this->second / 86400;

        if ($tmpYear >= 1582.288) { // YYYY-MM-DD >= 1582-10-15
            $a = (int)($y / 100);
            $b = 2 - $a + (int)($a / 4);
        } elseif ($tmpYear <= 1582.277) { // YY-MM-DD <= 1582-10-04
            $b = 0;
        } else {
            throw new \Exception('Date between 1582-10-04 and 1582-10-15 is not defined.');
        }

        $JD = (int)(365.25 * ($y + 4716)) + (int)(30.6001 * ($m + 1)) + $d + $h + $b - 1524.5;

        return $JD;
    }


    public function getJulianDay0(): float // TODO float or int
    {
        return $this->getJulianDay(true);
    }


    public function getJulianCenturiesSinceJ2000(): float
    {
        $jd = $this->getJulianDay();
        $T = ($jd - 2451545.0) / 36525.0;

        return $T;
    }


    public function getDayOfWeek(): int
    {
        $jd = $this->getJulianDay();
        $dow = ($jd + 1.5) % 7;

        return $dow;
    }


    public function getDayOfWeekString(): string
    {
        $dow = $this->getDayOfWeek();

        switch ($dow) {
            case 0:
                return 'Sunday';
            case 1:
                return 'Monday';
            case 2:
                return 'Tuesday';
            case 3:
                return 'Wednesday';
            case 4:
                return 'Thursday';
            case 5:
                return 'Friday';
            case 6:
                return 'Saturday';
            default;
                return 'Wrong day of week: ' . $dow;
                break;
        }
    }


    public function getDayOfYear(): int
    {
        $k = $this->isLeapYear($this->year) ? 1 : 2;
        $n = (int)((275 * $this->month) / 9) - $k * (int)(($this->month + 9) / 12) + $this->day - 30;

        return $n;
    }


    public function getUniversalTime(): int
    {
        // TODO write
        $ut = 0;
        return $ut;
    }


    public function getGreenwichMeanSiderealTime(bool $normalized = true): float
    {
        $JD = $this->getJulianDay();
        $T = $this->getJulianCenturiesSinceJ2000();

        $t0 = 280.46061837 + 360.98564736629 * ($JD - 2451545) + 0.000387933 * pow($T, 2) + pow($T, 3) / 38710000;

        if ($normalized) {
            $t0 = Util::normalizeAngle($t0);
        }

        return $t0;
    }


    public function getGreenwichMeanSiderealTimeInHours(): float
    {
        $gmst = $this->getGreenwichMeanSiderealTime();
        $t0 = $gmst / 15;

        return $t0;
    }


    public function getApparentGreenwichMeanSiderealTime(bool $normalized = true): float
    {
        $gmst = $this->getGreenwichMeanSiderealTime($normalized);

        $earth = new Earth();
        $earth->setTimeOfInterest($this);

        $p = $earth->getNutation();
        $e = deg2rad($earth->getTrueObliquityOfEcliptic());

        $gmst = $gmst + $p * cos($e);

        return $gmst;
    }


    public function getLocalMeanSiderealTime(float $lon, bool $normalized = true): float
    {
        $lonEast = $lon >= 0 ? $lon : 360 + $lon;

        $gmst = $this->getGreenwichMeanSiderealTime(false);
        $lmst = $gmst + $lonEast;

        if ($normalized) {
            $lmst = Util::normalizeAngle($lmst);
        }

        return $lmst;
    }


    public function getLocalMeanSiderealTimeInHours(float $lon): float
    {
        $lmst = $this->getLocalMeanSiderealTime($lon);
        $lmst = $lmst / 15;

        return $lmst;
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
}
