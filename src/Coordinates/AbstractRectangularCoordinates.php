<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

abstract class AbstractRectangularCoordinates
{
    protected $x = 0;
    protected $y = 0;
    protected $z = 0;

    public function __construct(float $x, float $y, float $z)
    {
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
