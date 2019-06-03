<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Utils\AngleUtil;

abstract class AbstractEclipticalSphericalCoordinates
{
    protected $longitude = 0.0;
    protected $latitude = 0.0;
    protected $radiusVector = 0.0;

    public function __construct(float $longitude, float $latitude, float $radiusVector = 0.0)
    {
        $this->longitude = $longitude;
        $this->latitude = $latitude;
        $this->radiusVector = $radiusVector;
    }

    public function __toString()
    {
        return 'Longitude: ' . AngleUtil::dec2angle($this->longitude) . "\n"
            . 'Latitude: ' . AngleUtil::dec2angle($this->latitude) . "\n"
            . 'Radius Vector: ' . $this->radiusVector . "\n";
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getRadiusVector(): float
    {
        return $this->radiusVector;
    }
}
