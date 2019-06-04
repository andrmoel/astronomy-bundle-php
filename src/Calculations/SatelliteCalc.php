<?php

namespace Andrmoel\AstronomyBundle\Calculations;

use Andrmoel\AstronomyBundle\Constants;
use Andrmoel\AstronomyBundle\Entities\TwoLineElements;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;
use Andrmoel\AstronomyBundle\Utils\GeneralUtil;

class SatelliteCalc
{
    public static function twoLineElements2keplerianElements(TwoLineElements $tle, float $T): array
    {
        $T0 = $tle->getEpoch()->getJulianCenturiesFromJ2000();
        $M0 = $tle->getMeanAnomaly();
        $n = $tle->getMeanMotion();
        $d1mm = $tle->get1thDerivativeOfMeanMotion();
        $d2mm = $tle->get2ndDerivativeOfMeanMotion();

        $dT = $T - $T0;

        $M = self::getMeanAnomaly($dT, $M0, $n, $d1mm, $d2mm);

        var_dump($M);
        die();
        // TODO ...
        return [];
    }

    private static function getMeanAnomaly(float $dT, float $M0, float $n, float $d1mm, float $d2mm): float
    {
        $M = $M0
            + $n * $dT
            + $d1mm * pow($dT, 2)
            + $d2mm * pow($dT, 3);

        return $M;
    }

    private static function getTrueAnomaly(float $E, float $e): float
    {
        $ERad = deg2rad($E);

        $ThetaRad = 2 * atan(
                sqrt((1 + $e) / (1 - $e)) * tan($ERad / 2)
            );

        $Theta = rad2deg($ThetaRad);

        return $Theta;
    }

    private static function getEccentricAnomaly(float $M, float $e): float
    {
        $MRad = deg2rad($M);

        $Ei = $M + 0.85 * $e * GeneralUtil::sign(sin($MRad));

        do {
            $EiRad = deg2rad($Ei);

            $Ej = $Ei - ($Ei - $e * sin($EiRad) - $M) / (1 - $e * cos($EiRad));
            $Ei = $Ej;
        } while (abs($Ei - $Ej) > 1e-8);

        $Ei = AngleUtil::normalizeAngle($Ei);

        return $Ei;
    }

    private static function getSemiMajorAxis(float $dT, float $n, float $d1mm, float $d2mm): float
    {
        $GM = Constants::GRAVITATIONAL_PARAMETER;

        $a0 = pow(sqrt($GM / $n), 2 / 3);

        $term = $n
            + 2 * $d1mm * $dT
            + 3 * $d2mm * pow($dT, 2);

        $a = $a0 * pow($n * pow($term, -1), 2 / 3);

        return $a;
    }
}