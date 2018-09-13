<?php

namespace Andrmoel\AstronomyBundle;

class Util
{
    public static function angle2dec(int $deg, int $min, float $sec): float
    {
        $angle = $deg + $min / 60 + $sec / 3600;

        return $angle;
    }

    public static function dec2angle(float $dec): string
    {
        $deg = (int)$dec;
        $x = ($dec - $deg) * 60;
        $min = (int)$x;
        $sec = ($x - $min) * 60;

        $angle = $deg . '°' . $min . '\'' . $sec . '"';

        return $angle;
    }

    public static function time2angleDec(int $hour, int $min, float $sec): float
    {
        $time = $hour + $min / 60 + $sec / 3600;

        return $time * 15;
    }

    public static function normalizeAngle(float $angle, float $nAngle = 360.0): float
    {
        $angle = fmod($angle, $nAngle);
        if ($angle < 0) {
            $angle = $angle + $nAngle;
        }

        return $angle;
    }
}
