<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 04.01.15
 * Time: 13:55
 */

namespace App\Util\Astro;

use App\Util\Astro\AstronomicalObjects\Earth;

/**
 * Class TimeOfInterest
 * @package Astro
 */
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


    /**
     * Constructor
     */
    public function __construct()
    {
        $tmpTime = date('Y,m,d,H,i,s', time());
        $tmpArr = explode(',', $tmpTime);

        $this->year = (int) $tmpArr[0];
        $this->month = (int) $tmpArr[1];
        $this->day = (int) $tmpArr[2];
        $this->hour = (int) $tmpArr[3];
        $this->minute = (int) $tmpArr[4];
        $this->second = (int) $tmpArr[5];
    }

    /**
     * Set time
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @param int $second
     */
    public function setTime($year = 0, $month = 0, $day = 0, $hour = 0, $minute = 0, $second = 0)
    {
        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
        $this->hour = $hour;
        $this->minute = $minute;
        $this->second = $second;
    }


    /**
     * Set time by day of year
     * @param int $year
     * @param int $dayOfYear
     * @param int $hour
     * @param int $minute
     * @param int $second
     */
    public function setTimeByDayOfYear($year = 0, $dayOfYear = 0, $hour = 0, $minute = 0, $second = 0)
    {
        $k = $this->isLeapYear($this->year) ? 1 : 2;
        $month = $dayOfYear < 32 ? 1 : (int) ((9 * ($k + $dayOfYear)) / 275 + 0.98);
        $day = $dayOfYear - (int) ((275 * $month) / 9) + $k * (int) (($month + 9) / 12) + 30;

        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
        $this->hour = $hour;
        $this->minute = $minute;
        $this->second = $second;
    }


    /**
     * Set date time
     * @param \DateTime $date
     */
    public function setDateTime(\DateTime $dateTime)
    {
        $this->year = $dateTime->format('Y');
        $this->month = $dateTime->format('m');
        $this->day = $dateTime->format('d');
        $this->hour = $dateTime->format('H');
        $this->minute = $dateTime->format('i');
        $this->second = $dateTime->format('s');
    }


    /**
     * Set julian day
     * @param double $jd
     */
    public function setJulianDay($jd)
    {
        $jd = $jd + 0.5;
        $z = (int) $jd;
        $f = $jd - $z;

        $a = $z;
        if ($z < 2299161) {
            $a = $z;
        } elseif ($z >= 2291161) {
            $a = (int) (($z - 1867216.25) / 36524.25);
            $a = $z + 1 + $a - (int) ($a / 4);
        }

        $b = $a + 1524;
        $c = (int) (($b - 122.1) / 365.25);
        $d = (int) (365.25 * $c);
        $e = (int) (($b - $d) / 30.6001);

        $dayOfMonth = $b - $d - (int) (30.6001 * $e) + $f;
        $month = $e < 14 ? $e - 1 : $e - 13;
        $year = $month > 2 ? $c - 4716 : $c - 4715;
        $hour = ($dayOfMonth - (int) $dayOfMonth) * 24;
        $minute = ($hour - (int) $hour) * 60;
        $second = ($minute - (int) $minute) * 60;

        $this->year = $year;
        $this->month = (int) $month;
        $this->day = (int) $dayOfMonth;
        $this->hour = (int) $hour;
        $this->minute = (int) $minute;
        $this->second = (int) $second;
    }


    /**
     * Set UNIX time
     * @param $unixTime
     */
    public function setUnixTime($unixTime)
    {
        $tmpTime = date('Y,m,d,H,i,s', $unixTime);
        $tmpArr = explode(',', $tmpTime);

        $this->year = (int) $tmpArr[0];
        $this->month = (int) $tmpArr[1];
        $this->day = (int) $tmpArr[2];
        $this->hour = (int) $tmpArr[3];
        $this->minute = (int) $tmpArr[4];
        $this->second = (int) $tmpArr[5];
    }


    /**
     * Set TLE epoch
     * @param string $epoch
     */
    public function setTleEpoch($epoch)
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
        $hour = ($decimalPart - (int) $decimalPart) * 24;
        $minute = ($hour - (int) $hour) * 60;
        $second = ($minute - (int) $minute) * 60;

        $this->year = $year;
        $this->month = $toi->getMonth();
        $this->day = $toi->getDay();
        $this->hour = (int) $hour;
        $this->minute = (int) $minute;
        $this->second = (int) $second;
    }


    /**
     * Get year
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }


    /**
     * Get month
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }


    /**
     * Get day
     * @return int
     */
    public function getDay()
    {
        return $this->day;
    }


    /**
     * Get hour
     * @return int
     */
    public function getHour()
    {
        return $this->hour;
    }


    /**
     * Get minute
     * @return int
     */
    public function getMinute()
    {
        return $this->minute;
    }


    /**
     * Get second
     * @return int
     */
    public function getSecond()
    {
        return $this->second;
    }


    /**
     * Get date time
     * @return \DateTime
     */
    public function getDateTime()
    {
        $dateTime = new \DateTime();
        $dateTime->setDate($this->year, $this->month, $this->day);
        $dateTime->setTime($this->hour, $this->minute, $this->second);

        return $dateTime;
    }


    /**
     * Get time string
     * @return string
     */
    public function getTimeString()
    {
        $timeStr = $this->year . '-';
        $timeStr .= substr('0' . $this->month, -2) . '-';
        $timeStr .= substr('0' . $this->day, -2) . ' ';
        $timeStr .= substr('0' . $this->hour, -2) . ':';
        $timeStr .= substr('0' . $this->minute, -2) . ':';
        $timeStr .= substr('0' . $this->second, -2);

        return $timeStr;
    }


    /**
     * Get formatted time string
     * @param $format
     * @return string
     */
    public function getFormattedTimeString($format)
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


    /**
     * Get julian day
     * @param bool $jd0
     * @return int
     * @throws \Exception
     */
    public function getJulianDay($jd0 = false)
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
            $a = (int) ($y / 100);
            $b = 2 - $a + (int) ($a / 4);
        } elseif ($tmpYear <= 1582.277) { // YY-MM-DD <= 1582-10-04
            $b = 0;
        } else {
            throw new \Exception('Date between 1582-10-04 and 1582-10-15 is not defined.');
        }

        $JD = (int) (365.25 * ($y + 4716)) + (int) (30.6001 * ($m + 1)) + $d + $h + $b - 1524.5;

        return $JD;
    }


    /**
     * Get julian day for time 00:00:00
     * @return double
     */
    public function getJulianDay0()
    {
        return $this->getJulianDay(true);
    }


    /**
     * Get number of julian centuries since J2000.0
     * @return float
     */
    public function getJulianCenturiesSinceJ2000()
    {
        $jd = $this->getJulianDay();
        $T =  ($jd - 2451545.0) / 36525.0;

        return $T;
    }


    /**
     * Get day of week
     * @return int
     */
    public function getDayOfWeek()
    {
        $jd = $this->getJulianDay();
        $dow = ($jd + 1.5) % 7;

        return $dow;
    }


    /**
     * Get day of week string
     * @return string
     */
    public function getDayOfWeekString()
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


    /**
     * Get day of year
     * @return int
     */
    public function getDayOfYear()
    {
        $k = $this->isLeapYear($this->year) ? 1 : 2;
        $n = (int) ((275 * $this->month) / 9) - $k * (int) (($this->month + 9) / 12) + $this->day - 30;

        return $n;
    }


    /**
     * Get universal time (UT)
     * @return int
     */
    public function getUniversalTime()
    {
        // TODO
        $ut = 0;
        return $ut;
    }


    /**
     * Get greenwich mean sidereal time (GMST) [°]
     * @param bool $normalized
     * @return float
     */
    public function getGreenwichMeanSiderealTime($normalized = true)
    {
        $JD = $this->getJulianDay();
        $T = $this->getJulianCenturiesSinceJ2000();

        $t0 = 280.46061837 + 360.98564736629 * ($JD - 2451545) + 0.000387933 * pow($T, 2) + pow($T, 3) / 38710000;

        if ($normalized) {
            $t0 = Util::normalizeAngle($t0);
        }

        return $t0;
    }


    /**
     * Get greenwich mean sidereal time (GMST) [hours]
     * @return float
     */
    public function getGreenwichMeanSiderealTimeInHours()
    {
        $gmst = $this->getGreenwichMeanSiderealTime();
        $t0 = $gmst / 15;

        return $t0;
    }


    /**
     * Get apparent greenwich mean sidereal time (GMST) [°]
     * @param bool $normalized
     * @return float
     */
    public function getApparentGreenwichMeanSiderealTime($normalized = true)
    {
        $gmst = $this->getGreenwichMeanSiderealTime($normalized);

        $earth = new Earth();
        $earth->setTimeOfInterest($this);

        $p = $earth->getNutation();
        $e = deg2rad($earth->getTrueObliquityOfEcliptic());

        $gmst = $gmst + $p * cos($e);

        return $gmst;
    }


    /**
     * Get local mean sidereal time (LMST) [°]
     * @param double $lon longitude of observer
     * @param bool $normalized
     * @return double
     */
    public function getLocalMeanSiderealTime($lon, $normalized = true)
    {
        $lonEast = $lon >= 0 ? $lon : 360 + $lon;

        $gmst = $this->getGreenwichMeanSiderealTime(false);
        $lmst = $gmst + $lonEast;

        if ($normalized) {
            $lmst = Util::normalizeAngle($lmst);
        }

        return $lmst;
    }


    /**
     * Get local mean sidereal time (LMST) [hours]
     * @param $lon
     * @return float
     */
    public function getLocalMeanSiderealTimeInHours($lon)
    {
        $lmst = $this->getLocalMeanSiderealTime($lon);
        $lmst = $lmst / 15;

        return $lmst;
    }


    /**
     * Is a leap year
     * @param $year
     * @return bool
     */
    public function isLeapYear($year)
    {
        if ($year / 4 != (int) ($year / 4)) {
            return false;
        } elseif ($year / 100 != (int) ($year / 100)) {
            return true;
        } elseif ($year / 400 != (int) ($year / 400)) {
            return false;
        } else {
            return true;
        }
    }
}
