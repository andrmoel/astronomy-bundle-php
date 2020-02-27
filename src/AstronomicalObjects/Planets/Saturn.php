<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\Calculations\VSOP87\SaturnRectangularVSOP87;
use Andrmoel\AstronomyBundle\Calculations\VSOP87\SaturnSphericalVSOP87;
use Andrmoel\AstronomyBundle\TimeOfInterest;

class Saturn extends Planet
{
    protected $VSOP87_SPHERICAL = SaturnSphericalVSOP87::class;
    protected $VSOP87_RECTANGULAR = SaturnRectangularVSOP87::class;

    public static function create(TimeOfInterest $toi = null): self
    {
        return new self($toi);
    }
}
