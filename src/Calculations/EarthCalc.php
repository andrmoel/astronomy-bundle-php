<?php

namespace Andrmoel\AstronomyBundle\Calculations;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Earth;
use Andrmoel\AstronomyBundle\CalculationCache;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

class EarthCalc implements EarthCalcInterface
{
    /**
     * Same as sun's
     * @return float
     */
    public static function getMeanAnomaly(float $T): float
    {
        if (CalculationCache::has('earthMeanAnomaly', $T)) {
            return CalculationCache::get('earthMeanAnomaly', $T);
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

        CalculationCache::set('earthMeanAnomaly', $T, $M);

        return $M;
    }

    public static function getEccentricity(float $T): float
    {
        if (CalculationCache::has('earthEccentricity', $T)) {
            return CalculationCache::get('earthEccentricity', $T);
        }

        // Meeus 25.4
        $e = 0.016708634
            - 0.000042037 * $T
            - 0.0000001267 * pow($T, 2);

        CalculationCache::set('earthEccentricity', $T, $e);

        return $e;
    }

    public static function getLongitudeOfPerihelionOfOrbit(float $T): float
    {
        if (CalculationCache::has('earthLongitudeOfPerihelionOfOrbit', $T)) {
            return CalculationCache::get('earthLongitudeOfPerihelionOfOrbit', $T);
        }

        // Meeus 23
        $pi = 102.93735
            + 1.71946 * $T
            + 0.00046 * pow($T, 2);

        CalculationCache::set('earthEccentricity', $T, $pi);

        return $pi;
    }

    public static function getMeanObliquityOfEcliptic(float $T): float
    {
        if (CalculationCache::has('earthMeanObliquityOfEcliptic', $T)) {
            return CalculationCache::get('earthMeanObliquityOfEcliptic', $T);
        }

        $U = $T / 100;

        // Meeus 22.3
        $eps0 = 84381.448
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
        $eps0 = $eps0 / 3600;

        CalculationCache::set('earthMeanObliquityOfEcliptic', $T, $eps0);

        return $eps0;
    }

    public static function getTrueObliquityOfEcliptic(float $T): float
    {
        if (CalculationCache::has('earthTrueObliquityOfEcliptic', $T)) {
            return CalculationCache::get('earthTrueObliquityOfEcliptic', $T);
        }

        $eps0 = self::getMeanObliquityOfEcliptic($T);
        $sumEps = self::getNutationInObliquity($T);

        // Meeus chapter 22
        $eps = $eps0 + $sumEps;

        CalculationCache::set('earthTrueObliquityOfEcliptic', $T, $eps);

        return $eps;
    }

    public static function getNutationInLongitude(float $T): float
    {
        if (CalculationCache::has('earthNutationInLongitude', $T)) {
            return CalculationCache::get('earthNutationInLongitude', $T);
        }

        // Meeus chapter 22
        $D = MoonCalc::getMeanElongation($T);
        $Msun = SunCalc::getMeanAnomaly($T);
        $Mmoon = MoonCalc::getMeanAnomaly($T);
        $F = MoonCalc::getArgumentOfLatitude($T);
        // Longitude of the ascending node of moon's mean orbit on ecliptic
        $O = 125.04452
            - 1934.136261 * $T
            + 0.0020708 * pow($T, 2)
            + pow($T, 3) / 450000;

        $sumPhi = 0;
        foreach (self::ARGUMENTS_NUTATION as $args) {
            $argMmoon = $args[0]; // Mean anomaly of moon
            $argMsun = $args[1]; // Mean anomaly of sun
            $argF = $args[2]; // Mean argument of perigee
            $argD = $args[3]; // Mean elongation of moon
            $argO = $args[4]; // Mean length of ascending knot of moon's orbit
            $argPhi1 = $args[5];
            $argPhi2 = $args[6];

            $tmpSum = $argD * $D + $argMsun * $Msun + $argMmoon * $Mmoon + $argF * $F + $argO * $O;

            $sumPhi += sin(deg2rad($tmpSum)) * ($argPhi1 + $argPhi2 * $T);
        }

        $sumPhi *= 0.0001 / 3600;

        CalculationCache::set('earthNutationInLongitude', $T, $sumPhi);

        return $sumPhi;
    }

    public static function getNutationInObliquity(float $T): float
    {
        if (CalculationCache::has('earthNutationInObliquity', $T)) {
            return CalculationCache::get('earthNutationInObliquity', $T);
        }

        // Meeus chapter 22
        $D = MoonCalc::getMeanElongation($T);
        $Msun = SunCalc::getMeanAnomaly($T);
        $Mmoon = MoonCalc::getMeanAnomaly($T);
        $F = MoonCalc::getArgumentOfLatitude($T);
        // Longitude of the ascending node of moon's mean orbit on ecliptic
        $O = 125.04452
            - 1934.136261 * $T
            + 0.0020708 * pow($T, 2)
            + pow($T, 3) / 450000;

        $sumEps = 0;
        foreach (self::ARGUMENTS_NUTATION as $args) {
            $argMmoon = $args[0]; // Mean anomaly of moon
            $argMsun = $args[1]; // Mean anomaly of sun
            $argF = $args[2]; // Mean argument of perigee
            $argD = $args[3]; // Mean elongation of moon
            $argO = $args[4]; // Mean length of ascending knot of moon's orbit
            $argEps1 = $args[7];
            $argEps2 = $args[8];

            $tmpSum = $argD * $D + $argMsun * $Msun + $argMmoon * $Mmoon + $argF * $F + $argO * $O;

            $sumEps += cos(deg2rad($tmpSum)) * ($argEps1 + $argEps2 * $T);
        }

        $sumEps *= 0.0001 / 3600;

        CalculationCache::set('earthNutationInObliquity', $T, $sumEps);

        return $sumEps;
    }

    /**
     * Get distance between 2 points on earths surface [km]
     * @param Location $location1
     * @param Location $location2
     * @return float
     */
    public static function getDistanceBetweenLocations(Location $location1, Location $location2): float
    {
        $lat1 = $location1->getLatitude();
        $lon1 = $location1->getLongitude();
        $lat2 = $location2->getLatitude();
        $lon2 = $location2->getLongitude();

        // Meeus 11.1
        $F = deg2rad(($lat1 + $lat2) / 2);
        $G = deg2rad(($lat1 - $lat2) / 2);
        $l = deg2rad(($lon1 - $lon2) / 2);

        $S = pow(sin($G), 2) * pow(cos($l), 2) + pow(cos($F), 2) * pow(sin($l), 2);
        $C = pow(cos($G), 2) * pow(cos($l), 2) + pow(sin($F), 2) * pow(sin($l), 2);

        $o = atan(sqrt($S / $C));
        $R = sqrt($S * $C) / $o;

        $D = 2 * $o * (Earth::RADIUS / 100);
        $H1 = (3 * $R - 1) / (2 * $C);
        $H2 = (3 * $R + 1) / (2 * $S);

        $s = $D * (1 + Earth::FLATTENING * $H1 * pow(sin($F), 2) * pow(cos($G), 2)
                - Earth::FLATTENING * $H2 * pow(cos($F), 2) * pow(sin($G), 2));

        return $s / 10;
    }
}
