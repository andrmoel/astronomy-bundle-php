<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\Calculations\VSOP87Calc;

class Venus extends Planet
{
    protected $VSOP87_SPHERICAL = VSOP87Calc::PLANET_VENUS_SPHERICAL;
    protected $VSOP87_RECTANGULAR = VSOP87Calc::PLANET_VENUS_RECTANGULAR;
}
