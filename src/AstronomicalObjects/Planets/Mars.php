<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\Calculations\VSOP87\MarsRectangularVSOP87;
use Andrmoel\AstronomyBundle\Calculations\VSOP87\MarsSphericalVSOP87;
use Andrmoel\AstronomyBundle\TimeOfInterest;

class Mars extends Planet
{
    protected $VSOP87_SPHERICAL = MarsSphericalVSOP87::class;
    protected $VSOP87_RECTANGULAR = MarsRectangularVSOP87::class;

    public static function create(TimeOfInterest $toi = null): self
    {
        return new self($toi);
    }
}
