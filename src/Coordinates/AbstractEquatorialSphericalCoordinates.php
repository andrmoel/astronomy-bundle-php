<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Utils\AngleUtil;

abstract class AbstractEquatorialSphericalCoordinates
{
    protected $rightAscension = 0;
    protected $declination = 0;
    protected $radiusVector = 0;

    public function __construct(float $rightAscension, float $declination, float $radiusVector = 0.0)
    {
        $this->rightAscension = $rightAscension;
        $this->declination = $declination;
        $this->radiusVector = $radiusVector;
    }

    public function __toString()
    {
        return 'Right Ascension: ' . AngleUtil::dec2time($this->rightAscension) . "\n"
            . 'Declination: ' . AngleUtil::dec2angle($this->declination) . "\n"
            . 'Radius Vector: ' . $this->radiusVector . "\n";
    }

    public function getRightAscension(): float
    {
        return $this->rightAscension;
    }

    public function getDeclination(): float
    {
        return $this->declination;
    }

    public function getRadiusVector(): float
    {
        return $this->radiusVector;
    }
}
