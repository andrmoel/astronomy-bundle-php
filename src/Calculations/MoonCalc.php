<?php

namespace Andrmoel\AstronomyBundle\Calculations;

use Andrmoel\AstronomyBundle\Utils\AngleUtil;

class MoonCalc implements MoonCalcInterface
{
    public static function getSumL(float $T): float
    {
        // Meeus 47.B
        $L = MoonCalc::getMeanLongitude($T);
        $D = MoonCalc::getMeanElongationFromSun($T);
        $Msun = SunCalc::getMeanAnomaly($T);
        $Mmoon = MoonCalc::getMeanAnomaly($T);
        $F = MoonCalc::getArgumentOfLatitude($T);

        // Action of venus
        $A1 = 119.75 + 131.849 * $T;
        // Action of jupiter
        $A2 = 53.09 + 479264.290 * $T;
        $E = 1 - 0.002516 * $T - 0.0000074 * pow($T, 2);

        $sumL = 3958 * sin(deg2rad($A1))
            + 1962 * sin(deg2rad($L - $F))
            + 318 * sin(deg2rad($A2));

        foreach (self::ARGUMENTS_LR as $arg) {
            $argD = $arg[0];
            $argMsun = $arg[1];
            $argMmoon = $arg[2];
            $argF = $arg[3];
            $argSumL = $arg[4];

            $tmpSumL = sin(deg2rad($argD * $D + $argMsun * $Msun + $argMmoon * $Mmoon + $argF * $F));

            switch ($argMsun) {
                case 1:
                case -1:
                    $tmpSumL = $tmpSumL * $argSumL * $E;
                    break;
                case 2:
                case -2:
                    $tmpSumL = $tmpSumL * $argSumL * $E * $E;
                    break;
                default:
                    $tmpSumL = $tmpSumL * $argSumL;
                    break;
            }

            $sumL += $tmpSumL;
        }

        return $sumL;
    }

    public static function getSumR(float $T): float
    {
        // Meeus 47.B
        $D = MoonCalc::getMeanElongationFromSun($T);
        $Msun = SunCalc::getMeanAnomaly($T);
        $Mmoon = MoonCalc::getMeanAnomaly($T);
        $F = MoonCalc::getArgumentOfLatitude($T);

        // Action of jupiter
        $E = 1 - 0.002516 * $T - 0.0000074 * pow($T, 2);

        $sumR = 0;

        foreach (self::ARGUMENTS_LR as $arg) {
            $argD = $arg[0];
            $argMsun = $arg[1];
            $argMmoon = $arg[2];
            $argF = $arg[3];
            $argSumR = $arg[5];

            $tmpSumR = cos(deg2rad($argD * $D + $argMsun * $Msun + $argMmoon * $Mmoon + $argF * $F));

            switch ($argMsun) {
                case 1:
                case -1:
                    $tmpSumR = $tmpSumR * $argSumR * $E;
                    break;
                case 2:
                case -2:
                    $tmpSumR = $tmpSumR * $argSumR * $E * $E;
                    break;
                default:
                    $tmpSumR = $tmpSumR * $argSumR;
                    break;
            }

            $sumR += $tmpSumR;
        }

        return $sumR;
    }

    public static function getSumB(float $T): float
    {
        // Meeus 47.B
        $L = MoonCalc::getMeanLongitude($T);
        $D = MoonCalc::getMeanElongationFromSun($T);
        $Msun = SunCalc::getMeanAnomaly($T);
        $Mmoon = MoonCalc::getMeanAnomaly($T);
        $F = MoonCalc::getArgumentOfLatitude($T);

        // Action of venus
        $A1 = 119.75 + 131.849 * $T;
        // Action of jupiter
        $A3 = 313.45 + 481266.484 * $T;
        $E = 1 - 0.002516 * $T - 0.0000074 * pow($T, 2);

        $sumB = -2235 * sin(deg2rad($L))
            + 382 * sin(deg2rad($A3))
            + 175 * sin(deg2rad($A1 - $F))
            + 175 * sin(deg2rad($A1 + $F))
            + 127 * sin(deg2rad($L - $Mmoon))
            - 115 * sin(deg2rad($L + $Mmoon));

        foreach (self::ARGUMENTS_B as $arg) {
            $argD = $arg[0];
            $argMsun = $arg[1];
            $argMmoon = $arg[2];
            $argF = $arg[3];
            $argSumB = $arg[4];

            $tmpSumB = sin(deg2rad($argD * $D + $argMsun * $Msun + $argMmoon * $Mmoon + $argF * $F));

            switch ($argMsun) {
                case 1:
                case -1:
                    $tmpSumB = $tmpSumB * $argSumB * $E;
                    break;
                case 2:
                case -2:
                    $tmpSumB = $tmpSumB * $argSumB * pow($E, 2);
                    break;
                default:
                    $tmpSumB = $tmpSumB * $argSumB;
                    break;
            }

            $sumB += $tmpSumB;
        }

        return $sumB;
    }

    // TODO Belongs to sun?
    public static function getMeanElongationFromSun(float $T): float
    {
        // Meeus chapter 22
//        $D = 297.85036
//            + 445267.111480 * $T
//            - 0.0019142 * pow($T, 2)
//            + pow($T, 3) / 189474;

        // Meeus 47.2
        $D = 297.8501921
            + 445267.1114034 * $T
            - 0.0018819 * pow($T, 2)
            + pow($T, 3) / 545868
            - pow($T, 4) / 113065000;
        $D = AngleUtil::normalizeAngle($D);

        return $D;
    }

    public static function getMeanAnomaly(float $T): float
    {
        // Meeus chapter 22
//        $Mmoon = 134.96298
//            + 477198.867398 * $T
//            + 0.0086972 * pow($T, 2)
//            + pow($T, 3) / 56250;

        // Meeus 47.2
        $Mmoon = 134.9633964
            + 477198.8675055 * $T
            + 0.0087414 * pow($T, 2)
            + pow($T, 3) / 69699
            - pow($T, 4) / 1471200;
        $Mmoon = AngleUtil::normalizeAngle($Mmoon);

        return $Mmoon;
    }

    public static function getArgumentOfLatitude(float $T): float
    {
        // Meeus chapter 22
//        $F = 93.27191
//            + 483202.017538 * $T
//            - 0.0036825 * pow($T, 2)
//            + pow($T, 3) / 327270;

        // Meeus 47.5
        $F = 93.2720950
            + 483202.0175233 * $T
            - 0.0036539 * pow($T, 2)
            - pow($T, 3) / 352600
            + pow($T, 4) / 86331000;
        $F = AngleUtil::normalizeAngle($F);

        return $F;
    }

    public static function getMeanLongitude(float $T): float
    {
        // Meeus 47.1
        $L = 218.3164477
            + 481267.88123421 * $T
            - 0.0015786 * pow($T, 2)
            + pow($T, 3) / 538841
            - pow($T, 4) / 65194000;
        $L = AngleUtil::normalizeAngle($L);

        return $L;
    }

    /**
     * Get distance to earth [km]
     * @return float
     */
    public static function getDistanceToEarth(float $T): float
    {
        $sumR = self::getSumR($T);
        $d = (385000.56 + ($sumR / 1000));

        return $d;
    }

    public static function getEquatorialHorizontalParallax(float $T): float
    {
        $d = self::getDistanceToEarth($T);

        // Meeus 47
        $pi = rad2deg(asin(6378.14 / $d));

        return $pi;
    }

    public static function getLatitude(float $T): float
    {
        $sumB = self::getSumB($T);

        $b = $sumB / 1000000;

        return $b;
    }

    public static function getLongitude(float $T): float
    {
        $L = self::getMeanLongitude($T);
        $sumL = self::getSumL($T);

        $l = $L + ($sumL / 1000000);

        return $l;
    }

    public static function getApparentLongitude(float $T): float
    {
        $l = self::getLongitude($T);
        $phi = EarthCalc::getNutationInLongitude($T);

        $l = $l + $phi;

        return $l;
    }
}
