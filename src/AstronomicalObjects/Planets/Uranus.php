<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\Calculations\VSOP87\UranusRectangularVSOP87;
use Andrmoel\AstronomyBundle\Calculations\VSOP87\UranusSphericalVSOP87;
use Andrmoel\AstronomyBundle\TimeOfInterest;

class Uranus extends Planet
{
    protected $VSOP87_SPHERICAL = UranusSphericalVSOP87::class;
    protected $VSOP87_RECTANGULAR = UranusRectangularVSOP87::class;

    public static function create(TimeOfInterest $toi = null): self
    {
        return new self($toi);
    }
}
