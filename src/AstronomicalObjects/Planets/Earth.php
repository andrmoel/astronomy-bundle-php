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
}
