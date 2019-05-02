<?php

namespace Andrmoel\AstronomyBundle\Calculations;

use Andrmoel\AstronomyBundle\Utils\AngleUtil;
use Andrmoel\AstronomyBundle\Utils\DistanceUtil;

class SunCalc
{
    public static function getMeanLongitude(float $T): float
    {
        $t = $T / 10;

        // Meeus 28.2
        $L0 = 280.4664567
            + 360007.6982779 * $t
            + 0.03042028 * pow($t, 2)
            + pow($t, 3) / 49931
            - pow($t, 4) / 15300
            + pow($t, 5) / 2000000;
        $L0 = AngleUtil::normalizeAngle($L0);

        return $L0;
    }

    /**
     * Same as earth's
     * @return float
     */
    public static function getMeanAnomaly(float $T): float
    {
        // Meeus chapter 22
//        $M = 357.52772
//            + 35999.050340 * $T
//            - 0.0001603 * pow($T, 2)
//            - pow($T, 3) / 300000;

        // Meeus 47.4
        $M = 357.5291092
            + 35999.0502909 * $T
            - 0.0001536 * pow($T, 2)
            + pow($T, 3) / 2449000;
        $M = AngleUtil::normalizeAngle($M);

        return $M;
    }

    public static function getEquationOfCenter(float $T): float
    {
        $M = self::getMeanAnomaly($T);

        // Meeus 25.4
        $C = (1.914602 - 0.004817 * $T - 0.000014 * pow($T, 2)) * sin(deg2rad($M));
        $C += (0.019993 - 0.000101 * $T) * sin(2 * deg2rad($M));
        $C += 0.000289 * sin(3 * deg2rad($M));

        return $C;
    }

    public static function getTrueLongitude(float $T): float
    {
        // Meeus 25.4
        $L0 = self::getMeanLongitude($T);
        $C = self::getEquationOfCenter($T);

        $o = $L0 + $C;

        return $o;
    }

    public static function getTrueAnomaly(float $T): float
    {
        // Meeus 25.4
        $M = self::getMeanAnomaly($T);
        $C = self::getEquationOfCenter($T);

        $v = $M + $C;

        return $v;
    }

    public static function getRadiusVector(float $T): float
    {
        $e = EarthCalc::getEccentricity($T);
        $v = self::getTrueAnomaly($T);
        $vRad = deg2rad($v);

        // Meeus 25.5
        $R = (1000001018 * (1 - pow($e, 2))) / (1 + $e * cos($vRad));
        $R /= 1000000000;

        return $R;
    }

    /**
     * Get distance to earth [km]
     * @return float
     */
    public static function getDistanceToEarth(float $T): float
    {
        $R = self::getRadiusVector($T);
        $r = DistanceUtil::au2km($R);

        return $r;
    }
}
