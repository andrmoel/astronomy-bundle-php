<?php

namespace Andrmoel\AstronomyBundle\Calculations;

use Andrmoel\AstronomyBundle\Entities\Time;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

class TimeCalc
{
    public static function julianDay2julianDay0(float $JD): float
    {
        $JD0 = floor($JD + 0.5) - 0.5;

        return $JD0;
    }

    public static function julianDay2ModifiedJulianDay(float $JD): float
    {
        $MJD = $JD - 2400000.5;

        return $MJD;
    }

    public static function time2julianDay(Time $time): float
    {
        $tmpYear = floatval($time->year . '.' . self::getDayOfYear($time));

        if ($time->month > 2) {
            $Y = $time->year;
            $M = $time->month;
        } else {
            $Y = $time->year - 1;
            $M = $time->month + 12;
        }

        $D = $time->day;
        $H = $time->hour / 24 + $time->minute / 1440 + $time->second / 86400;

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

    public static function julianDay2time(float $JD): Time
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

        $time = new Time((int)$year, (int)$month, (int)$dayOfMonth, (int)$hour, (int)$minute, (int)$second);

        return $time;
    }

    public static function julianDay2julianCenturiesJ2000(float $JD): float
    {
        $T = ($JD - 2451545.0) / 36525.0;

        return $T;
    }

    public static function julianCenturiesJ20002julianDay(float $T): float
    {
        $JD = $T * 36525.0 + 2451545.0;

        return $JD;
    }

    public static function julianDay2julianMillenniaJ2000(float $JD): float
    {
        $T = self::julianDay2julianCenturiesJ2000($JD);

        $t = $T / 10;

        return $t;
    }

    public static function julianMillenniaJ20002julianDay(float $t): float
    {
        $T = $t * 10;

        $JD = self::julianCenturiesJ20002julianDay($T);

        return $JD;
    }

    public static function getGreenwichMeanSiderealTime(float $T): float
    {
        $JD = self::julianCenturiesJ20002julianDay($T);

        // Meeus 12.4
        $GMST = 280.46061837
            + 360.98564736629 * ($JD - 2451545)
            + 0.000387933 * pow($T, 2)
            + pow($T, 3) / 38710000;
        $GMST = AngleUtil::normalizeAngle($GMST);

        return $GMST;
    }

    public static function getGreenwichApparentSiderealTime(float $T): float
    {
        $t0 = self::getGreenwichMeanSiderealTime($T);
        $p = EarthCalc::getNutationInLongitude($T);
        $e = EarthCalc::getTrueObliquityOfEcliptic($T);

        $eRad = deg2rad($e);

        // Meeus 12
        $GAST = $t0 + $p * cos($eRad);

        return $GAST;
    }

    /**
     * Get equation of time [degrees]
     * @param float $T
     * @return float
     */
    public static function getEquationOfTimeInDegrees(float $T): float
    {
        $L0 = SunCalc::getMeanLongitude($T);
        $rightAscension = SunCalc::getApparentRightAscension($T);

        // TODO Use method with higher accuracy (Meeus p.166) 25.9
//        $rightAscension = 198.378178;

        $dPhi = EarthCalc::getNutationInLongitude($T);
        $e = EarthCalc::getTrueObliquityOfEcliptic($T);
        $eRad = deg2rad($e);

        // Meeus 28.1
        $E = $L0 - 0.0057183 - $rightAscension + $dPhi * cos($eRad);

        return $E;
    }

    public static function getDeltaT(int $year, int $month = 0): float
    {
        // https://eclipse.gsfc.nasa.gov/SEcat5/deltatpoly.html
        $y = $year + ($month - 0.5) / 12;

        if ($year < -500) {
            $u = ($y - 1820) / 100;
            $deltaT = -20
                + 32 * pow($u, 2);
        }

        if ($year >= -500 && $year < 500) {
            $u = $y / 100;
            $deltaT = 10583.6
                - 1014.41 * $u
                + 33.78311 * pow($u, 2)
                - 5.952053 * pow($u, 3)
                - 0.1798452 * pow($u, 4)
                + 0.022174192 * pow($u, 5)
                + 0.0090316521 * pow($u, 6);
        }

        if ($year >= 500 && $year < 1600) {
            $u = ($y - 1000) / 100;
            $deltaT = 1574.2
                - 556.01 * $u
                + 71.23472 * pow($u, 2)
                + 0.319781 * pow($u, 3)
                - 0.8503463 * pow($u, 4)
                - 0.005050998 * pow($u, 5)
                + 0.0083572073 * pow($u, 6);
        }

        if ($year >= 1600 && $year < 1700) {
            $t = $y - 1600;
            $deltaT = 120
                - 0.9808 * $t
                - 0.01532 * pow($t, 2)
                + pow($t, 3) / 7129;
        }

        if ($year >= 1700 && $year < 1800) {
            $t = $y - 1700;
            $deltaT = 8.83
                + 0.1603 * $t
                - 0.0059285 * pow($t, 2)
                + 0.00013336 * pow($t, 3)
                - pow($t, 4) / 1174000;
        }

        if ($year >= 1800 && $year < 1860) {
            $t = $y - 1800;
            $deltaT = 13.72
                - 0.332447 * $t
                + 0.0068612 * pow($t, 2)
                + 0.0041116 * pow($t, 3)
                - 0.00037436 * pow($t, 4)
                + 0.0000121272 * pow($t, 5)
                - 0.0000001699 * pow($t, 6)
                + 0.000000000875 * pow($t, 7);
        }

        if ($year >= 1860 && $year < 1900) {
            $t = $y - 1860;

            $deltaT = 7.62
                + 0.5737 * $t
                - 0.251754 * pow($t, 2)
                + 0.01680668 * pow($t, 3)
                - 0.0004473624 * pow($t, 4)
                + pow($t, 5) / 233174;
        }

        if ($year >= 1900 && $year < 1920) {
            $t = $y - 1900;
            $deltaT = -2.79
                + 1.494119 * $t
                - 0.0598939 * pow($t, 2)
                + 0.0061966 * pow($t, 3)
                - 0.000197 * pow($t, 4);
        }

        if ($year >= 1920 && $year < 1941) {
            $t = $y - 1920;
            $deltaT = 21.20
                + 0.84493 * $t
                - 0.076100 * pow($t, 2)
                + 0.0020936 * pow($t, 3);
        }

        if ($year >= 1941 && $year < 1961) {
            $t = $y - 1950;
            $deltaT = 29.07
                + 0.407 * $t
                - pow($t, 2) / 233
                + pow($t, 3) / 2547;
        }

        if ($year >= 1961 && $year < 1986) {
            $t = $y - 1975;
            $deltaT = 45.45
                + 1.067 * $t
                - pow($t, 2) / 260
                - pow($t, 3) / 718;
        }

        if ($year >= 1986 && $year < 2005) {
            $t = $y - 2000;
            $deltaT = 63.86
                + 0.3345 * $t
                - 0.060374 * pow($t, 2)
                + 0.0017275 * pow($t, 3)
                + 0.000651814 * pow($t, 4)
                + 0.00002373599 * pow($t, 5);
        }

        if ($year >= 2005 && $year < 2050) {
            $t = $y - 2000;
            $deltaT = 62.92
                + 0.32217 * $t
                + 0.005589 * pow($t, 2);
        }

        if ($year >= 2050) {
            $u = ($y - 1820) / 100;
            $deltaT = -20
                + 32 * pow($u, 2);
        }

        return $deltaT;
    }

    public static function dayOfYear2time(int $year, float $dayOfYear): Time
    {
        // Meeus 7
        $K = self::isLeapYear($year) ? 1 : 2;
        $month = $dayOfYear < 32 ? 1 : (int)((9 * ($K + $dayOfYear)) / 275 + 0.98);
        $day = $dayOfYear - (int)((275 * $month) / 9) + $K * (int)(($month + 9) / 12) + 30;

        $hourFloat = 24 * ($dayOfYear - floor($dayOfYear));
        $hour = floor($hourFloat);
        $minuteFloat = 60 * ($hourFloat - $hour);
        $minute = floor($minuteFloat);
        $second = 60 * ($minuteFloat - $minute);
        $second = round($second);

        return new Time($year, $month, (int)$day, (int)$hour, (int)$minute, (int)$second);
    }


    public static function getDayOfYear(Time $time): int
    {
        $K = self::isLeapYear($time->year) ? 1 : 2;
        $M = $time->month;
        $D = $time->day;

        // Meeus 7.f
        $N = (int)((275 * $M) / 9) - $K * (int)(($M + 9) / 12) + $D - 30;

        return $N;
    }

    public static function getDayOfWeek(float $JD): int
    {
        // Meeus 7.e
        $DOW = (int)($JD + 1.5) % 7;

        return $DOW;
    }

    public static function isLeapYear(int $year): bool
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

    public static function yearTwoDigits2year(int $yearTwoDigits): int
    {
        if (preg_match('/^([0-9]+)[0-9]{2}$/', date('Y'), $matches)) {
            $yearHundreds = (int)$matches[1];
            if ($yearTwoDigits > 50) {
                $yearHundreds--;
            }

            return $yearHundreds * 100 + $yearTwoDigits;
        }

        throw new \Exception('Could not convert two digit year to year');
    }
}
