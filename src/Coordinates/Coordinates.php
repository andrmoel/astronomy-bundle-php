<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Earth;
use Andrmoel\AstronomyBundle\TimeOfInterest;

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
