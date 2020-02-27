<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\Calculations\VSOP87\EarthRectangularVSOP87;
use Andrmoel\AstronomyBundle\Calculations\VSOP87\EarthSphericalVSOP87;
use Andrmoel\AstronomyBundle\TimeOfInterest;

class Earth extends Planet
{
    const RADIUS = 6378137.0; // Earth radius in km
    const FLATTENING = 0.00335281317789691440603238146967; // (1 / 298.257) Earth's flattening
    const EARTH_AXIS_RATIO = 0.996647189335;

    protected $VSOP87_SPHERICAL = EarthSphericalVSOP87::class;
    protected $VSOP87_RECTANGULAR = EarthRectangularVSOP87::class;

    public static function create(TimeOfInterest $toi = null): self
    {
        return new self($toi);
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
