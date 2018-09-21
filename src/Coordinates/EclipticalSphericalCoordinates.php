<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

class EclipticalSphericalCoordinates
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

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function getRadiusVector(): float
    {
        return $this->radiusVector;
    }
}
