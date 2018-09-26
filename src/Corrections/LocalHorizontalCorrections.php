<?php

namespace Andrmoel\AstronomyBundle\Corrections;

use Andrmoel\AstronomyBundle\Coordinates\LocalHorizontalCoordinates;

class LocalHorizontalCorrections
{
    public function correctAtmosphericRefraction(LocalHorizontalCoordinates $coordinates): LocalHorizontalCoordinates
    {
        $az = $coordinates->getAzimuth();
        $alt = $coordinates->getAltitude();

        // Meeus 16.4
        $R = 1.02 / tan(deg2rad($alt + (10.3 / ($alt + 5.11))));

        $alt += $R / 60;

        return new LocalHorizontalCoordinates($az, $alt);
    }
}
