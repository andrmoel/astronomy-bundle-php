<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\TimeOfInterest;
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

    public function getGeocentricEclipticalSphericalCoordinates(): GeocentricEclipticalSphericalCoordinates
    {
        // Meeus 33.2
        $lon = atan2($this->Y, $this->X);
        $lon = rad2deg($lon);
        $lon = AngleUtil::normalizeAngle($lon);

        $lat = atan($this->Z / sqrt(pow($this->X, 2) + pow($this->Y, 2)));
        $lat = rad2deg($lat);

        $radiusVector = sqrt(pow($this->X, 2) + pow($this->Y, 2) + pow($this->Z, 2));

        return new GeocentricEclipticalSphericalCoordinates($lon, $lat, $radiusVector);
    }

    public function getGeocentricEquatorialCoordinates(TimeOfInterest $toi): GeocentricEquatorialCoordinates
    {
        return $this
            ->getGeocentricEclipticalSphericalCoordinates()
            ->getGeocentricEquatorialCoordinates($toi);
    }
}
