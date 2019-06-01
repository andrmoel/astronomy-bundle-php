<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

class GeocentricEquatorialRectangularCoordinates
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

    public function getGeocentricEclipticalRectangularCoordinates(float $T): GeocentricEclipticalRectangularCoordinates
    {
        return $this
            ->getGeocentricEclipticalSphericalCoordinates($T)
            ->getGeocentricEclipticalRectangularCoordinates();
    }

    public function getGeocentricEclipticalSphericalCoordinates(float $T): GeocentricEclipticalSphericalCoordinates
    {
        return $this
            ->getGeocentricEquatorialSphericalCoordinates()
            ->getGeocentricEclipticalSphericalCoordinates($T);
    }

    public function getGeocentricEquatorialSphericalCoordinates(): GeocentricEquatorialSphericalCoordinates
    {
        // Meeus 33.2
        $rightAscensionRad = atan2($this->Y, $this->X);
        $rightAscension = AngleUtil::normalizeAngle(rad2deg($rightAscensionRad));

        $declinationRad = atan($this->Z / sqrt(pow($this->X, 2) + pow($this->Y, 2)));
        $declination = rad2deg($declinationRad);

        $radiusVector = sqrt(pow($this->X, 2) + pow($this->Y, 2) + pow($this->Z, 2));

        return new GeocentricEquatorialSphericalCoordinates($rightAscension, $declination, $radiusVector);
    }

    public function getLocalHorizontalCoordinates(Location $location, float $T): LocalHorizontalCoordinates
    {
        return $this
            ->getGeocentricEquatorialSphericalCoordinates()
            ->getLocalHorizontalCoordinates($location, $T);
    }
}
