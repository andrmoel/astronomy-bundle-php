<?php

namespace Andrmoel\AstronomyBundle\Calculations;

use Andrmoel\AstronomyBundle\CalculationCache;

class VSOP87Calc
{
    const PLANET_MERCURY_RECTANGULAR = 'VSOP87C.mer';
    const PLANET_VENUS_RECTANGULAR = 'VSOP87C.ven';
    const PLANET_EARTH_RECTANGULAR = 'VSOP87C.ear';
    const PLANET_MARS_RECTANGULAR = 'VSOP87C.mar';
    const PLANET_JUPITER_RECTANGULAR = 'VSOP87C.jup';
    const PLANET_SATURN_RECTANGULAR = 'VSOP87C.sat';
    const PLANET_URANUS_RECTANGULAR = 'VSOP87C.ura';
    const PLANET_NEPTUNE_RECTANGULAR = 'VSOP87C.nep';
    const PLANET_MERCURY_SPHERICAL = 'VSOP87D.mer';
    const PLANET_VENUS_SPHERICAL = 'VSOP87D.ven';
    const PLANET_EARTH_SPHERICAL = 'VSOP87D.ear';
    const PLANET_MARS_SPHERICAL = 'VSOP87D.mar';
    const PLANET_JUPITER_SPHERICAL = 'VSOP87D.jup';
    const PLANET_SATURN_SPHERICAL = 'VSOP87D.sat';
    const PLANET_URANUS_SPHERICAL = 'VSOP87D.ura';
    const PLANET_NEPTUNE_SPHERICAL = 'VSOP87D.nep';

    const COEFFICIENT_A = '1';
    const COEFFICIENT_B = '2';
    const COEFFICIENT_C = '3';

    public static function getVSOP87Result(string $VSOP87Data, float $t): array
    {
        if (CalculationCache::has($VSOP87Data, $t)) {
            return CalculationCache::get($VSOP87Data, $t);
        }

        $result = [
            self::COEFFICIENT_A => 0.0,
            self::COEFFICIENT_B => 0.0,
            self::COEFFICIENT_C => 0.0,
        ];

        $data = self::loadData($VSOP87Data);

        foreach ($data as $coefficient => $set) {
            $coefficientSolved = 0.0;
            foreach ($set as $tIndex => $coefficientsSets) {
                $x = self::sumUpCoefficients($coefficientsSets, $t);
                $coefficientSolved += $x * pow($t, $tIndex);
            }

            $result[$coefficient] = $coefficientSolved;
        }

        CalculationCache::set($VSOP87Data, $t, $result);

        return $result;
    }

    private static function sumUpCoefficients(array $coefficientsSet, float $t): float
    {
        $x = 0.0;

        foreach ($coefficientsSet as $key => $coefficients) {
            $A = $coefficients['A'];
            $B = $coefficients['B'];
            $C = $coefficients['C'];

            $x += self::calculateVSOP87Term($A, $B, $C, $t);
        }

        return $x;
    }

    private static function calculateVSOP87Term($A, $B, $C, $t): float
    {
        return $A * cos($B + $C * $t);
    }

    private static function loadData(string $VSOP87Data): array
    {
        return require __DIR__ . '/../Resources/VSOP87/' . $VSOP87Data . '.php';
    }
}
