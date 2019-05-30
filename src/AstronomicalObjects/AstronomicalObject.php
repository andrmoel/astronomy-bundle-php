<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects;

use Andrmoel\AstronomyBundle\TimeOfInterest;

abstract class AstronomicalObject
{
    /** @var TimeOfInterest */
    protected $toi;

    /** @var float julian day since J2000.0 */
    protected $T = 0;

    public function __construct(TimeOfInterest $toi = null)
    {
        $this->toi = $toi ? $toi : TimeOfInterest::createFromCurrentTime();
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
}
