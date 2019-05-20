<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\Calculations\VSOP87Calc;

class Uranus extends Planet
{
    protected $VSOP87_SPHERICAL = VSOP87Calc::PLANET_URANUS_SPHERICAL;
    protected $VSOP87_RECTANGULAR = VSOP87Calc::PLANET_URANUS_RECTANGULAR;
}
