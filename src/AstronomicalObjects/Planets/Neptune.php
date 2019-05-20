<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\Calculations\VSOP87Calc;

class Neptune extends Planet
{
    protected $VSOP87_SPHERICAL = VSOP87Calc::PLANET_NEPTUNE_SPHERICAL;
    protected $VSOP87_RECTANGULAR = VSOP87Calc::PLANET_NEPTUNE_RECTANGULAR;
}
