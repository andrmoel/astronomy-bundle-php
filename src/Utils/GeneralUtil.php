<?php

namespace Andrmoel\AstronomyBundle\Utils;

class GeneralUtil
{
    public static function year2string(float $year): string
    {
        $yearStr = str_pad(abs($year), 4, '0', STR_PAD_LEFT);
        $yearStr = $year < 0 ? '-' . $yearStr : $yearStr;

        return $yearStr;
    }
}
