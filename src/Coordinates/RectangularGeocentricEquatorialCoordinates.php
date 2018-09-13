<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\TimeOfInterest;

class RectangularGeocentricEquatorialCoordinates extends Coordinates
{
    private $x = 0;
    private $y = 0;
    private $z = 0;


    public function __construct(float $x, float $y, float $z)
    {
        parent::__construct();

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
