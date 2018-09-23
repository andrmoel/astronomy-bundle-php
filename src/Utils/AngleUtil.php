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

    public static function dec2angle(float $dec): string
    {
        $sign = $dec < 0 ? '-' : '';
        $dec = abs($dec);

        $deg = (int)$dec;
        $min = (int)(($dec - $deg) * 60);
        $sec = round(($dec - $deg - $min / 60) * 3600, 3);

        $angle = $sign . $deg . '°' . $min . '\'' . $sec . '"';

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
        $sign = $angle < 0 ? '-' : '';
        $time = abs($angle / 15);

        $hour = (int)$time;
        $min = (int)(($time - $hour) * 60);
        $sec = round(($time - $hour - $min / 60) * 3600, 3);

        $time = $sign . $hour . 'h' . $min . 'm' . $sec . 's';

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
