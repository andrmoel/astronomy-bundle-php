<?php

namespace Andrmoel\AstronomyBundle\Utils;

class AngleUtil
{
    public static function angle2dec(string $angle): float
    {
        if (preg_match('/(-?)([0-9]+)°.*?([0-9]+)\'.*?([0-9.]+)"/', $angle, $matches)) {
            $sign = trim($matches[1]) === '-' ? -1 : 1;
            $deg = (int)$matches[2];
            $min = (int)$matches[3];
            $sec = (float)$matches[4];

            $angle = $sign * ($deg + $min / 60 + $sec / 3600);
        } else {
            throw new \Exception('AngleUtil::angle2dec() false format');
        }

        return $angle;
    }

    // TODO FEHLER!!!! -24.929312194388)
    public static function dec2angle(float $dec): string
    {
        $sign = $dec < 0 ? '-' : '';

        $deg = (int)abs($dec);
        $x = ($dec - $deg) * 60;
        $min = (int)$x;
        $sec = round(($x - $min) * 60, 3);

        $angle = $sign . abs($deg) . '°' . abs($min) . '\'' . abs($sec) . '"';

        return $angle;
    }

    public static function time2dec(string $timeAngle): float
    {
        if (preg_match('/(-?)([0-9]+)h.*?([0-9]+)m.*?([0-9.]+)s/', $timeAngle, $matches)) {
            $sign = trim($matches[1]) === '-' ? -1 : 1;
            $deg = (int)$matches[2];
            $min = (int)$matches[3];
            $sec = (float)$matches[4];

            $angle = $sign * ($deg + $min / 60 + $sec / 3600);
            $angle *= 15;
        } else {
            throw new \Exception('AngleUtil::time2dec() false format');
        }

        return $angle;
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

    public static function normalizeAngle(float $angle, float $baseAngle = 360.0): float
    {
        $angle = fmod($angle, $baseAngle);
        if ($angle < 0) {
            $angle = $angle + $baseAngle;
        }

        return $angle;
    }
}
