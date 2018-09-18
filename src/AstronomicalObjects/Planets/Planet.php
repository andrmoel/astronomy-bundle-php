<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\AstronomicalObjects\AstronomicalObject;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

abstract class Planet extends AstronomicalObject
{
    // Meeus Appendix III for planets
    protected $argumentsL = null;
    protected $argumentsB = null;
    protected $argumentsR = null;

    public function getEclipticalLongitude()
    {
        $L = $this->resolveTerms($this->argumentsL);
        $L = rad2deg($L);
        $L = AngleUtil::normalizeAngle($L);

        return $L;
    }

    public function getEclipticalLatitude()
    {
        $B = $this->resolveTerms($this->argumentsB);
        $B = rad2deg($B);

        return $B;
    }

    public function getRadiusVector()
    {
        $R = $this->resolveTerms($this->argumentsR);

        return $R;
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
