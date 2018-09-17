<?php

namespace Andrmoel\AstronomyBundle\Angle;

class Angle
{
    private $angleDec;

    public function __construct(float $angleDec)
    {
        $this->angleDec = $angleDec;
    }

    public static function create(int $deg, int $min, float $sec): self
    {
        $angle = $deg + $min / 60 + $sec / 3600;

        return new self($angle);
    }

    public function getAngleDec(): float
    {
        return $this->angleDec;
    }

    public function getAngleDegMinSec(): string
    {
        $deg = (int)$this->angleDec;
        $x = ($this->angleDec - $deg) * 60;
        $min = (int)$x;
        $sec = round(($x - $min) * 60, 3);

        $angle = $deg . '°' . $min . '\'' . $sec . '"';

        return $angle;
    }

    public function getAngleRadian(): float
    {
        return deg2rad($this->angleDec);
    }
}