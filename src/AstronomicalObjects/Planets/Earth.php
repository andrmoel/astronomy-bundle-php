<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\Calculations\VSOP87Calc;

class Earth extends Planet
{
    const RADIUS = 6378137.0; // Earth radius in km
    const FLATTENING = 0.00335281317789691440603238146967; // (1 / 298.257) Earth's flattening
    const EARTH_AXIS_RATIO = 0.996647189335;

    protected $VSOP87_SPHERICAL = VSOP87Calc::PLANET_EARTH_SPHERICAL;
    protected $VSOP87_RECTANGULAR = VSOP87Calc::PLANET_EARTH_RECTANGULAR;

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
