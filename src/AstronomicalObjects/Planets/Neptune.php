<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\Calculations\VSOP87\NeptuneRectangularVSOP87;
use Andrmoel\AstronomyBundle\Calculations\VSOP87\NeptuneSphericalVSOP87;

class Neptune extends Planet
{
    protected $VSOP87_SPHERICAL = NeptuneSphericalVSOP87::class;
    protected $VSOP87_RECTANGULAR = NeptuneRectangularVSOP87::class;
}
