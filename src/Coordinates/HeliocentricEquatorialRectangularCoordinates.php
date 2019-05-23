<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Utils\AngleUtil;

class HeliocentricEquatorialRectangularCoordinates
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

    public function getHeliocentricEclipticalRectangularCoordinates(): HeliocentricEclipticalRectangularCoordinates
    {
        // TODO
        return new HeliocentricEclipticalRectangularCoordinates(0, 0, 0);
    }

    public function getHeliocentricEclipticalSphericalCoordinates(): HeliocentricEclipticalSphericalCoordinates
    {
        // Meeus 33.2
        $longitude = atan($this->Y / $this->X);
        $longitude = rad2deg($longitude);
        $longitude = AngleUtil::normalizeAngle($longitude);

        $latitude = atan($this->Z / sqrt(pow($this->X, 2) + pow($this->Y, 2)));
        $latitude = rad2deg($latitude);

        $radiusVector = sqrt(pow($this->X, 2) + pow($this->Y, 2) + pow($this->Z, 2));

        return new HeliocentricEclipticalSphericalCoordinates($latitude, $longitude, $radiusVector);
    }

    // TODO
    public function getGeocentricEclipticalSphericalCoordinates(): GeocentricEclipticalSphericalCoordinates
    {
        return new GeocentricEclipticalSphericalCoordinates(0, 0, 0);
    }

    // TODO
    public function getGeocentricEquatorialRectangularCoordinates(): GeocentricEquatorialRectangularCoordinates
    {
        return new GeocentricEquatorialRectangularCoordinates(0, 0, 0);
    }

    // TODO
    public function getGeocentricEquatorialSphericalCoordinates(): GeocentricEquatorialSphericalCoordinates
    {
        return new GeocentricEquatorialSphericalCoordinates(0, 0, 0);
    }
}
