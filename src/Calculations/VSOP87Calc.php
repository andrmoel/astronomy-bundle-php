<?php

namespace Andrmoel\AstronomyBundle\Calculations;

use Andrmoel\AstronomyBundle\Calculations\VSOP87\VSOP87Interface;

class VSOP87Calc
{
    public static function solve(string $VSOP87, float $t): array
    {
        /** @var VSOP87Interface $VSOP87 */

        $A = $VSOP87::calculateA0($t)
            + $VSOP87::calculateA1($t) * $t
            + $VSOP87::calculateA2($t) * pow($t, 2)
            + $VSOP87::calculateA3($t) * pow($t, 3)
            + $VSOP87::calculateA4($t) * pow($t, 4);

        $B = $VSOP87::calculateB0($t)
            + $VSOP87::calculateB1($t) * $t
            + $VSOP87::calculateB2($t) * pow($t, 2)
            + $VSOP87::calculateB3($t) * pow($t, 3)
            + $VSOP87::calculateB4($t) * pow($t, 4);

        $R = $VSOP87::calculateC0($t)
            + $VSOP87::calculateC1($t) * $t
            + $VSOP87::calculateC2($t) * pow($t, 2)
            + $VSOP87::calculateC3($t) * pow($t, 3)
            + $VSOP87::calculateC4($t) * pow($t, 4);

        return [$A, $B, $R];
    }
}
