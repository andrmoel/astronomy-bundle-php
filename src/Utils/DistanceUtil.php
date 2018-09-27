<?php

namespace Andrmoel\AstronomyBundle\Utils;

class DistanceUtil
{
    private const AU_UNIT_OF_LENGTH = 149597870700.0;

    /**
     * // https://www.iau.org/static/resolutions/IAU2012_English.pdf
     * @param float $R
     * @return float
     */
    public static function au2km(float $R): float
    {
        return $R * (self::AU_UNIT_OF_LENGTH / 1000);
    }

    /**
     * // https://www.iau.org/static/resolutions/IAU2012_English.pdf
     * @param float $km
     * @return float
     */
    public static function km2au(float $km): float
    {
        return $km / (self::AU_UNIT_OF_LENGTH / 1000);
    }
}
