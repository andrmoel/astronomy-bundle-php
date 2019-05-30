<?php

namespace Andrmoel\AstronomyBundle\Corrections;

use Andrmoel\AstronomyBundle\Coordinates\LocalHorizontalCoordinates;

class LocalHorizontalCorrections
{
    public static function correctAtmosphericRefraction(
        LocalHorizontalCoordinates $coordinates
    ): LocalHorizontalCoordinates
    {
        $azimuth = $coordinates->getAzimuth();
        $altitude = $coordinates->getAltitude();

        // Meeus 16.4
        $R = 1.02 / tan(deg2rad($altitude + (10.3 / ($altitude + 5.11))));

        $altitude += $R / 60;

        return new LocalHorizontalCoordinates($azimuth, $altitude);
    }
}
