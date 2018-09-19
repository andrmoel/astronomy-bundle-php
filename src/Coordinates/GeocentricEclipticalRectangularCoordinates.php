<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Utils\AngleUtil;

class GeocentricEclipticalRectangularCoordinates
{
    private $X = 0;
    private $Y = 0;
    private $Z = 0;

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

    // TODO Test schreiben
    public function getGeocentricEclipticalSphericalCoordinates(): GeocentricEclipticalSphericalCoordinates
    {
        // Meeus 33.2
        $longitude = atan($this->Y / $this->X);
        $longitude = rad2deg($longitude);
        $longitude = AngleUtil::normalizeAngle($longitude);

        $latitude = atan($this->Z / sqrt(pow($this->X, 2) + pow($this->Y, 2)));
        $latitude = rad2deg($latitude);

        $radiusVector = sqrt(pow($this->X, 2) + pow($this->Y, 2) + pow($this->Z, 2));

        return new GeocentricEclipticalSphericalCoordinates($longitude, $latitude, $radiusVector);
    }
}
