<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 04.01.15
 * Time: 15:19
 */

namespace App\Util\Astro\AstronomicalObjects;

use App\Util\Astro\TimeOfInterest;
use App\Util\Astro\AstronomicalObjectCache;

/**
 * Class AstronomicalObject
 * @package AstroBundle\AstronomicalObjects
 */
class AstronomicalObject
{
    /** @var TimeOfInterest */
    protected $toi;

    /** @var double julian day since J2000.0 */
    protected $T = 0;

    /** @var AstronomicalObjectCache */
    protected $cache;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->toi = new TimeOfInterest();
        $this->cache = new AstronomicalObjectCache($this);
    }


    /**
     * Set time of interest
     * @param TimeOfInterest $toi
     */
    public function setTimeOfInterest($toi)
    {
        $this->toi = $toi;
        $this->T = $toi->getJulianCenturiesSinceJ2000();
    }


    /**
     * Get time of interest
     * @return TimeOfInterest
     */
    public function getTimeOfInterest()
    {
        return $this->toi;
    }
}
