<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

class EquatorialRectangularCoordinates
{
    private $X = 0.0;
    private $Y = 0.0;
    private $Z = 0.0;

    public function __construct(float $X, float $Y, float $Z)
    {
        $this->X = $X;
        $this->Y = $Y;
        $this->Z = $Z;
    }

    public function getX(): float
    {
        return $this->X;
    }

    public function getY(): float
    {
        return $this->Y;
    }

    public function getZ(): float
    {
        return $this->Z;
    }
}
