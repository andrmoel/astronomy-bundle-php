<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\Calculations\VSOP87\UranusRectangularVSOP87;
use Andrmoel\AstronomyBundle\Calculations\VSOP87\UranusSphericalVSOP87;

class Uranus extends Planet
{
    protected $VSOP87_SPHERICAL = UranusSphericalVSOP87::class;
    protected $VSOP87_RECTANGULAR = UranusRectangularVSOP87::class;
}
