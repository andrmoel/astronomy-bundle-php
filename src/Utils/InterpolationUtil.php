<?php

namespace Andrmoel\AstronomyBundle\Utils;

class InterpolationUtil
{
    public static function interpolate(float $y1, float $y2, float $y3, float $m, $n): float
    {
        // Meeus 3.3
        $a = $y2 - $y1;
        $b = $y3 - $y2;
        $c = $b - $a;

        $y = $y2 + 0.5 * $n * ($a + $b + $n * $c);

        return $y;
    }
}
