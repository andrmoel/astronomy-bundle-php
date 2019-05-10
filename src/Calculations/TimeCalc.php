<?php

namespace Andrmoel\AstronomyBundle\Calculations;

class TimeCalc
{
    public static function getJulianCenturiesFromJ2000(float $jd): float
    {
        $T = ($jd - 2451545.0) / 36525.0;

        return $T;
    }

    public function getJulianDay(float $T): float
    {
        $jd = $T * 36525.0 + 451545.0;

        return $jd;
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
}
