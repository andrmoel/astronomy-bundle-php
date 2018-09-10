<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 19.01.15
 * Time: 22:38
 */

namespace App\Util\Astro\Coordinates;

use App\Util\Astro\TimeOfInterest;

/**
 * Class GeocentricCoordinates
 * @package Coordinates
 */
class GeocentricCoordinates extends Coordinates
{
    private $X = 0;
    private $Y = 0;
    private $Z = 0;


    /**
     * Constructor
     * @param float $X
     * @param float $Y
     * @param float $Z
     * @param TimeOfInterest $toi
     */
    public function __construct($X, $Y, $Z, TimeOfInterest $toi)
    {
        parent::__construct($toi);

        $this->X = $X;
        $this->Y = $Y;
        $this->Z = $Z;
    }


    /**
     * Get X
     * @return float
     */
    public function getX()
    {
        return $this->X;
    }


    /**
     * Get Y
     * @return float
     */
    public function getY()
    {
        return $this->Y;
    }


    /**
     * Get Z
     * @return float
     */
    public function getZ()
    {
        return $this->Z;
    }
}
