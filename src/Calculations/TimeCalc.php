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
}
