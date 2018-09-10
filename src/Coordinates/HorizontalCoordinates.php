<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\TimeOfInterest;

class HorizontalCoordinates extends Coordinates
{
    private $azimuth = 0;
    private $altitude = 0;


    /**
     * Constructor
     * @param float $azimuth
     * @param float $altitude
     * @param TimeOfInterest $toi
     */
    public function __construct($azimuth, $altitude, TimeOfInterest $toi)
    {
        parent::__construct($toi);

        $this->azimuth = $azimuth;
        $this->altitude = $altitude;
    }


    /**
     * Get azimuth
     * @return float
     */
    public function getAzimuth()
    {
        return $this->azimuth;
    }


    /**
     * Get altitude
     * @return mixed
     */
    public function getAltitude()
    {
        return $this->altitude;
    }
}
