<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects;

use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\AstronomicalObjectCache;

class AstronomicalObject
{
    /** @var TimeOfInterest */
    protected $toi;

    /** @var double julian day since J2000.0 */
    protected $T = 0;

    /** @var AstronomicalObjectCache */
    protected $cache;

    public function __construct(TimeOfInterest $toi = null)
    {
        $this->toi = $toi ? $toi : new TimeOfInterest();
        $this->T = $this->toi->getJulianCenturiesSinceJ2000();
        $this->cache = new AstronomicalObjectCache($this);
    }

    public function setTimeOfInterest(TimeOfInterest $toi): void
    {
        $this->toi = $toi;
        $this->T = $this->toi->getJulianCenturiesSinceJ2000();
    }

    public function getTimeOfInterest(): TimeOfInterest
    {
        return $this->toi;
    }
}
