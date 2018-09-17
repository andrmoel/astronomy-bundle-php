<?php

namespace Andrmoel\AstronomyBundle\Utils;

class AngleUtil
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
        $sec = round(($x - $min) * 60, 3);

        $angle = $deg . '°' . $min . '\'' . $sec . '"';

        return $angle;
    }

    public static function time2dec(int $hour, int $min, float $sec): float
    {
        $time = $hour + $min / 60 + $sec / 3600;
        $time *= 15;

        return $time;
    }

    public static function dec2time(float $angle): string
    {
        $time = $angle / 15;

        $hour = (int)$time;
        $x = ($time - $hour) * 60;
        $min = (int)$x;
        $sec = round(($x - $min) * 60, 3);

        $time = $hour . 'h' . abs($min) . 'm' . abs($sec) . 's';

        return $time;
    }

    public static function normalizeAngle(float $angle): float
    {
        $baseAngle = 360.0;
        $angle = fmod($angle, $baseAngle);
        if ($angle < 0) {
            $angle = $angle + $baseAngle;
        }

        return $angle;
    }
}
