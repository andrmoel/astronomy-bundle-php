<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\TimeOfInterest;

class GeocentricRectangularCoordinates extends Coordinates
{
    private $rightAscension = 0;
    private $Declination = 0;
    private $distance = 0;


    public function __construct(float $x, float $y, float $z, TimeOfInterest $toi)
    {
        parent::__construct($toi);

        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }


    public function getX(): float
    {
        return $this->x;
    }


    public function getY(): float
    {
        return $this->y;
    }


    public function getZ(): float
    {
        return $this->z;
    }
}
