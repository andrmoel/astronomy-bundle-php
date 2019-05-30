<?php

namespace Andrmoel\AstronomyBundle\Calculations;

use Andrmoel\AstronomyBundle\CalculationCache;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;
use Andrmoel\AstronomyBundle\Utils\DistanceUtil;

class SunCalc
{
    /**
     * Same as earth's
     * @return float
     */
    public static function getMeanAnomaly(float $T): float
    {
        if (CalculationCache::has('sunMeanAnomaly', $T)) {
            return CalculationCache::get('sunMeanAnomaly', $T);
        }

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

        CalculationCache::set('sunMeanAnomaly', $T, $M);

        return $M;
    }

    public static function getTrueAnomaly(float $T): float
    {
        if (CalculationCache::has('sunTrueAnomaly', $T)) {
            return CalculationCache::get('sunTrueAnomaly', $T);
        }

        // Meeus 25.4
        $M = self::getMeanAnomaly($T);
        $C = self::getEquationOfCenter($T);

        $v = $M + $C;

        CalculationCache::set('sunTrueAnomaly', $T, $v);

        return $v;
    }

    public static function getMeanLongitude(float $T): float
    {
        if (CalculationCache::has('sunMeanLongitude', $T)) {
            return CalculationCache::get('sunMeanLongitude', $T);
        }

        $t = $T / 10;

        // Meeus 28.2
        $L0 = 280.4664567
            + 360007.6982779 * $t
            + 0.03042028 * pow($t, 2)
            + pow($t, 3) / 49931
            - pow($t, 4) / 15300
            + pow($t, 5) / 2000000;
        $L0 = AngleUtil::normalizeAngle($L0);

        CalculationCache::set('sunMeanLongitude', $T, $L0);

        return $L0;
    }

    public static function getTrueLongitude(float $T): float
    {
        if (CalculationCache::has('sunTrueLongitude', $T)) {
            return CalculationCache::get('sunTrueLongitude', $T);
        }

        // Meeus 25.4
        $L0 = self::getMeanLongitude($T);
        $C = self::getEquationOfCenter($T);

        $o = $L0 + $C;

        CalculationCache::set('sunTrueLongitude', $T, $o);

        return $o;
    }

    public static function getApparentLongitude(float $T): float
    {
        if (CalculationCache::has('sunApparentLongitude', $T)) {
            return CalculationCache::get('sunApparentLongitude', $T);
        }

        // Meeus 25.5
        $o = self::getTrueLongitude($T);

        $omega = 125.04 - 1934.136 * $T;
        $omegaRad = deg2rad($omega);

        $lon = $o - 0.00569 - 0.00478 * sin($omegaRad);

        CalculationCache::set('sunApparentLongitude', $T, $lon);

        return $lon;
    }

    public static function getEquationOfCenter(float $T): float
    {
        if (CalculationCache::has('sunEquationOfCenter', $T)) {
            return CalculationCache::get('sunEquationOfCenter', $T);
        }

        $M = self::getMeanAnomaly($T);

        // Meeus 25.4
        $C = (1.914602 - 0.004817 * $T - 0.000014 * pow($T, 2)) * sin(deg2rad($M));
        $C += (0.019993 - 0.000101 * $T) * sin(2 * deg2rad($M));
        $C += 0.000289 * sin(3 * deg2rad($M));

        CalculationCache::set('sunEquationOfCenter', $T, $C);

        return $C;
    }

    public static function getRadiusVector(float $T): float
    {
        if (CalculationCache::has('sunRadiusVector', $T)) {
            return CalculationCache::get('sunRadiusVector', $T);
        }

        $e = EarthCalc::getEccentricity($T);
        $v = self::getTrueAnomaly($T);
        $vRad = deg2rad($v);

        // Meeus 25.5
        $R = (1000001018 * (1 - pow($e, 2))) / (1 + $e * cos($vRad));
        $R /= 1000000000;

        CalculationCache::set('sunRadiusVector', $T, $R);

        return $R;
    }

    /**
     * Get distance to earth [km]
     * @return float
     */
    public static function getDistanceToEarth(float $T): float
    {
        if (CalculationCache::has('sunDistanceToEarth', $T)) {
            return CalculationCache::get('sunDistanceToEarth', $T);
        }

        $R = self::getRadiusVector($T);
        $r = DistanceUtil::au2km($R);

        CalculationCache::set('sunDistanceToEarth', $T, $r);

        return $r;
    }

    public static function getApparentRightAscension(float $T): float
    {
        if (CalculationCache::has('sunApparentRightAscension', $T)) {
            return CalculationCache::get('sunApparentRightAscension', $T);
        }

        // TODO Use method with higher accuracy (Meeus p.166) 25.9
        $lon = self::getApparentLongitude($T);
        $lonRad = deg2rad($lon);

        // Meeus 25.8 - Corrections
        $e = EarthCalc::getMeanObliquityOfEcliptic($T);
        $O = 125.04 - 1934.136 * $T;
        $ORad = deg2rad($O);
        $e = $e + 0.00256 * cos($ORad);
        $eRad = deg2rad($e);

        // Meeus 25.6
        $rightAscension = atan2(cos($eRad) * sin($lonRad), cos($lonRad));
        $rightAscension = AngleUtil::normalizeAngle(rad2deg($rightAscension));

        CalculationCache::set('sunApparentRightAscension', $T, $rightAscension);

        return $rightAscension;
    }

    public static function getApparentDeclination(float $T): float
    {
        if (CalculationCache::has('sunApparentRightDeclination', $T)) {
            return CalculationCache::get('sunApparentRightDeclination', $T);
        }

        // TODO Use method with higher accuracy (Meeus p.166) 25.9
        $lon = self::getApparentLongitude($T);
        $lonRad = deg2rad($lon);

        // Meeus 25.8 - Corrections
        $e = EarthCalc::getMeanObliquityOfEcliptic($T);
        $O = 125.04 - 1934.136 * $T;
        $ORad = deg2rad($O);
        $e = $e + 0.00256 * cos($ORad);
        $eRad = deg2rad($e);

        // Meeus 25.7
        $declination = asin(sin($eRad) * sin($lonRad));
        $declination = rad2deg($declination);

        CalculationCache::set('sunApparentRightDeclination', $T, $declination);

        return $declination;
    }
}
