<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects;

use Andrmoel\AstronomyBundle\TimeOfInterest;

abstract class AstronomicalObject
{
    /** @var TimeOfInterest */
    protected $toi;

    /** @var float julian day since J2000.0 */
    protected $T = 0;

    // Meeus Appendix III for planets
    protected $argumentsL = null;
    protected $argumentsB = null;
    protected $argumentsR = null;

    public function __construct(TimeOfInterest $toi = null)
    {
        $this->toi = $toi ? $toi : new TimeOfInterest();
        $this->T = $this->toi->getJulianCenturiesFromJ2000();
    }

    public function setTimeOfInterest(TimeOfInterest $toi): void
    {
        $this->toi = $toi;
        $this->T = $this->toi->getJulianCenturiesFromJ2000();
    }

    public function getTimeOfInterest(): TimeOfInterest
    {
        return $this->toi;
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
