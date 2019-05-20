<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\Calculations\VSOP87\SaturnRectangularVSOP87;
use Andrmoel\AstronomyBundle\Calculations\VSOP87\SaturnSphericalVSOP87;

class Saturn extends Planet
{
    protected $VSOP87_SPHERICAL = SaturnSphericalVSOP87::class;
    protected $VSOP87_RECTANGULAR = SaturnRectangularVSOP87::class;
}
