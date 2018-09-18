<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\AstronomicalObjects\AstronomicalObject;
use Andrmoel\AstronomyBundle\Coordinates\HeliocentricCoordinates;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

abstract class Planet extends AstronomicalObject
{
    // Meeus Appendix III for planets
    protected $argumentsL = null;
    protected $argumentsB = null;
    protected $argumentsR = null;

    public function getHeliocentricCoordinates()
    {
        $L = $this->resolveTerms($this->argumentsL);
        $L = rad2deg($L);
        $L = AngleUtil::normalizeAngle($L);

        $B = $this->resolveTerms($this->argumentsB);
        $B = rad2deg($B);

        $R = $this->resolveTerms($this->argumentsR);

        return new HeliocentricCoordinates($L, $B, $R);
    }

    protected function resolveTerms(array $terms): float
    {
        $t = $this->toi->getJulianMillenniaFromJ2000();

        // Meeus 32.2
        $sum = 0.0;
        foreach ($terms as $key => $arguments) {
            $value = $this->sumUpArguments($arguments);

            $sum += $value * pow($t, $key);
        }

        $sum /= pow(10, 8);

        return $sum;
    }

    protected function sumUpArguments(array $arguments): float
    {
        $t = $this->toi->getJulianMillenniaFromJ2000();

        // Meeus 21.1
        $sum = 0.0;
        foreach ($arguments as $argument) {
            $sum += $argument[0] * cos($argument[1] + $argument[2] * $t);
        }

        return $sum;
    }
}
