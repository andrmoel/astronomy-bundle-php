<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Calculations;

use Andrmoel\AstronomyBundle\Utils\AngleUtil;

class EarthCalculations
{
    /**
     * Same as sun's
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

    public static function getEccentricity(float $T): float
    {
        // Meeus 25.4
        $e = 0.016708634
            - 0.000042037 * $T
            - 0.0000001267 * pow($T, 2);

        return $e;
    }

    public static function getLongitudeOfPerihelionOfOrbit(float $T): float
    {
        // Meeus 23
        $pi = 102.93735 + 1.71946 * $T + 0.00046 * pow($T, 2);

        return $pi;
    }

    public static function getMeanObliquityOfEcliptic(float $T): float
    {
        $U = $T / 100;

        // Meeus 22.3
        $e0 = 84381.448
            - 4680.93 * $U
            - 1.55 * pow($U, 2)
            + 1999.25 * pow($U, 3)
            - 51.38 * pow($U, 4)
            - 249.67 * pow($U, 5)
            - 39.05 * pow($U, 6)
            + 7.12 * pow($U, 7)
            + 27.87 * pow($U, 8)
            + 5.79 * pow($U, 9)
            + 2.45 * pow($U, 10);
        $e0 = $e0 / 3600;

        return $e0;
    }
}
