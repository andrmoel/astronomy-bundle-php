<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\Location;

class Earth extends Planet
{
    const RADIUS = 6378137.0; // Earth radius in km
    const FLATTENING = 0.00335281317789691440603238146967; // (1 / 298.257) Earth's flattening
    const EARTH_AXIS_RATIO = 0.996647189335;

    public function loadVSOP87Data(): array
    {
        $data = file_get_contents(self::VSOP87_FILE_PATH . 'earth.json');

        return json_decode($data, 1);
    }

    /**
     * Get earth radius at equator
     * @return float
     */
    public function getRadius(): float
    {
        return self::RADIUS;
    }

    public function getFlattening(): float
    {
        return self::FLATTENING;
    }

    /**
     * Get distance between 2 points on earths surface [km]
     * @param Location $location1
     * @param Location $location2
     * @return float
     */
    public static function getDistance(Location $location1, Location $location2): float
    {
        $lat1 = $location1->getLatitude();
        $lon1 = $location1->getLongitude();
        $lat2 = $location2->getLatitude();
        $lon2 = $location2->getLongitude();

        // Meeus 11.1
        $F = deg2rad(($lat1 + $lat2) / 2);
        $G = deg2rad(($lat1 - $lat2) / 2);
        $l = deg2rad(($lon1 - $lon2) / 2);

        $S = pow(sin($G), 2) * pow(cos($l), 2) + pow(cos($F), 2) * pow(sin($l), 2);
        $C = pow(cos($G), 2) * pow(cos($l), 2) + pow(sin($F), 2) * pow(sin($l), 2);

        $o = atan(sqrt($S / $C));
        $R = sqrt($S * $C) / $o;

        $D = 2 * $o * (self::RADIUS / 100);
        $H1 = (3 * $R - 1) / (2 * $C);
        $H2 = (3 * $R + 1) / (2 * $S);

        $s = $D * (1 + self::FLATTENING * $H1 * pow(sin($F), 2) * pow(cos($G), 2)
                - self::FLATTENING * $H2 * pow(cos($F), 2) * pow(sin($G), 2));

        return $s / 10;
    }
}
