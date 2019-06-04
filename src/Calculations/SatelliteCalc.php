<?php

namespace Andrmoel\AstronomyBundle\Calculations;

use Andrmoel\AstronomyBundle\Entities\TwoLineElements;

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

        var_dump($M);die();
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
}