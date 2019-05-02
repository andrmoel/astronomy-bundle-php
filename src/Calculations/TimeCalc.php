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
}
