<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Location;
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
        $lonRad = atan2($this->Y, $this->X);
        $lon = AngleUtil::normalizeAngle(rad2deg($lonRad));

        $latRad = atan($this->Z / sqrt(pow($this->X, 2) + pow($this->Y, 2)));
        $lat = rad2deg($latRad);

        $r = sqrt(pow($this->X, 2) + pow($this->Y, 2) + pow($this->Z, 2));

        return new GeocentricEclipticalSphericalCoordinates($lat, $lon, $r);
    }

    public function getGeocentricEquatorialRectangularCoordinates(float $T): GeocentricEquatorialRectangularCoordinates
    {
        return $this
            ->getGeocentricEclipticalSphericalCoordinates()
            ->getGeocentricEquatorialRectangularCoordinates($T);
    }

    public function getGeocentricEquatorialSphericalCoordinates(float $T): GeocentricEquatorialSphericalCoordinates
    {
        return $this
            ->getGeocentricEclipticalSphericalCoordinates()
            ->getGeocentricEquatorialSphericalCoordinates($T);
    }

    public function getLocalHorizontalCoordinates(Location $location, float $T): LocalHorizontalCoordinates
    {
        return $this
            ->getGeocentricEclipticalSphericalCoordinates()
            ->getLocalHorizontalCoordinates($location, $T);
    }
}
