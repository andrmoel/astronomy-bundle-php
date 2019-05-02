<?php

namespace Andrmoel\AstronomyBundle\Calculations;

class TimeCalc
{
    public static function getJulianCenturiesFromJ2000(float $jd): float
    {
        $T = ($jd - 2451545.0) / 36525.0;

        return $T;
    }
}
