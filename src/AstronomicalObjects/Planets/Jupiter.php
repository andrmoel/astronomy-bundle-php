<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\Calculations\VSOP87\JupiterRectangularVSOP87;
use Andrmoel\AstronomyBundle\Calculations\VSOP87\JupiterSphericalVSOP87;

class Jupiter extends Planet
{
    protected $VSOP87_SPHERICAL = JupiterSphericalVSOP87::class;
    protected $VSOP87_RECTANGULAR = JupiterRectangularVSOP87::class;
}
