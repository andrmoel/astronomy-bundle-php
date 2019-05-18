<?php

namespace Andrmoel\AstronomyBundle\Calculations;

class VSOP87Calc
{
    const VSOP87_PLANET_MERCURY_HELIOCENTRIC_RECTANGULAR = 'VSOP87C.mer';
    const VSOP87_PLANET_VENUS_HELIOCENTRIC_RECTANGULAR = 'VSOP87C.ven';
    const VSOP87_PLANET_EARTH_HELIOCENTRIC_RECTANGULAR = 'VSOP87C.ear';
    const VSOP87_PLANET_MARS_HELIOCENTRIC_RECTANGULAR = 'VSOP87C.mar';
    const VSOP87_PLANET_JUPITER_HELIOCENTRIC_RECTANGULAR = 'VSOP87C.jup';
    const VSOP87_PLANET_SATURN_HELIOCENTRIC_RECTANGULAR = 'VSOP87C.sat';
    const VSOP87_PLANET_URANUS_HELIOCENTRIC_RECTANGULAR = 'VSOP87C.ura';
    const VSOP87_PLANET_NEPTUNE_HELIOCENTRIC_RECTANGULAR = 'VSOP87C.nep';
    const VSOP87_PLANET_MERCURY_HELIOCENTRIC_SPHERICAL = 'VSOP87D.mer';
    const VSOP87_PLANET_VENUS_HELIOCENTRIC_SPHERICAL = 'VSOP87D.ven';
    const VSOP87_PLANET_EARTH_HELIOCENTRIC_SPHERICAL = 'VSOP87D.ear';
    const VSOP87_PLANET_MARS_HELIOCENTRIC_SPHERICAL = 'VSOP87D.mar';
    const VSOP87_PLANET_JUPITER_HELIOCENTRIC_SPHERICAL = 'VSOP87D.jup';
    const VSOP87_PLANET_SATURN_HELIOCENTRIC_SPHERICAL = 'VSOP87D.sat';
    const VSOP87_PLANET_URANUS_HELIOCENTRIC_SPHERICAL = 'VSOP87D.ura';
    const VSOP87_PLANET_NEPTUNE_HELIOCENTRIC_SPHERICAL = 'VSOP87D.nep';

    const VSOP87_COEFFICIENT_A = '1';
    const VSOP87_COEFFICIENT_B = '2';
    const VSOP87_COEFFICIENT_C = '3';

    public static function getVSOP87Result(string $VSOP87Data, string $coefficient, float $T): float
    {
        $data = self::loadData($VSOP87Data);
        $data = $data[$coefficient];

        $result = 0.0;
        foreach ($data as $tIndex => $coefficientsSets) {
            $x = self::sumUpCoefficients($coefficientsSets, $T);
            $result += $x * pow($T, $tIndex);
        }

        return $result;
    }

    private static function sumUpCoefficients(array $coefficientsSet, float $T): float
    {
        $x = 0.0;

        foreach ($coefficientsSet as $key => $coefficients) {
            $A = $coefficients['A'];
            $B = $coefficients['B'];
            $C = $coefficients['C'];

            $x += self::calculateVSOP87Term($A, $B, $C, $T);
        }

        return $x;
    }

    private static function calculateVSOP87Term($A, $B, $C, $T): float
    {
        return $A * cos($B + $C * $T);
    }

    private static function loadData(string $VSOP87Data): array
    {
        return require __DIR__ . '/../Resources/VSOP87/' . $VSOP87Data . '.php';
    }
}
