<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\TimeOfInterest;

class LocalHorizontalCoordinates extends Coordinates
{
    private $azimuth = 0;
    private $altitude = 0;


    public function __construct(float $azimuth, float $altitude, TimeOfInterest $toi)
    {
        parent::__construct($toi);

        $this->azimuth = $azimuth;
        $this->altitude = $altitude;
    }


    public function getAzimuth(): float
    {
        return $this->azimuth;
    }


    public function getAltitude(): float
    {
        return $this->altitude;
    }

    public function getEquatorialCoordinates(): EquatorialCoordinates
    {
        $rightAscension = 0;
        $declination = 0;

        // TODO ... Meeus 94
        return new EquatorialCoordinates($rightAscension, $declination, $this->toi);
    }
}
