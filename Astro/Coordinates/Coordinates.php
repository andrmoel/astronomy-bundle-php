<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 10.01.15
 * Time: 11:59
 */

namespace App\Util\Astro\Coordinates;

use App\Util\Astro\AstronomicalObjects\Earth;
use App\Util\Astro\TimeOfInterest;

/**
 * Class Coordinates
 * @package Coordinates
 */
abstract class Coordinates
{
    /** @var TimeOfInterest */
    protected $toi;
    protected  $earth;


    /**
     * Constructor
     * @param TimeOfInterest $toi
     */
    public function __construct(TimeOfInterest $toi)
    {
        $this->toi = $toi;
        $this->earth = new Earth();
        $this->earth->setTimeOfInterest($toi);
    }


    /**
     * Set time of interest
     * @param TimeOfInterest $toi
     */
    public function setTimeOfInterest(TimeOfInterest $toi)
    {
        $this->toi = $toi;
        $this->earth->setTimeOfInterest($toi);
    }


    /**
     * Set equatorial coordinates
     * @param $RA
     * @param $dec
     */
    public function setEquatorialCoordinates($RA, $dec)
    {
        // TODO Needed???
        $this->rightAscension = $RA;
        $this->declination = $dec;
    }


    /**
     * Transform equatorial coordinates (RA/Dec) to horizontal coordinates (azimuth/altitude)
     */
    public static function equatorial2horizontal($RA, $dec)
    {
        // TODO
        $sinDec = sin($dec);
        $conDec = cos($dec);

        $az = 0;
        $alt = 0;

        $coord = array(
            'azimuth' => $az,
            'altitude' => $alt,
        );
        return $coord;
    }
}
