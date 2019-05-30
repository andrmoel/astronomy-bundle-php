<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\Calculations\VSOP87\MarsRectangularVSOP87;
use Andrmoel\AstronomyBundle\Calculations\VSOP87\MarsSphericalVSOP87;

class Mars extends Planet
{
    protected $VSOP87_SPHERICAL = MarsSphericalVSOP87::class;
    protected $VSOP87_RECTANGULAR = MarsRectangularVSOP87::class;
}
