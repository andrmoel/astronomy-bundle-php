<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\Calculations\VSOP87\VenusRectangularVSOP87;
use Andrmoel\AstronomyBundle\Calculations\VSOP87\VenusSphericalVSOP87;

class Venus extends Planet
{
    protected $VSOP87_SPHERICAL = VenusSphericalVSOP87::class;
    protected $VSOP87_RECTANGULAR = VenusRectangularVSOP87::class;
}
