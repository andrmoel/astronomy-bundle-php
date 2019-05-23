<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

class HeliocentricEclipticalSphericalCoordinates
{
    protected $latitude = 0.0;
    protected $longitude = 0.0;
    protected $radiusVector = 0.0;

    public function __construct(float $latitude, float $longitude, float $radiusVector = 0.0)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
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

    public function getHeliocentricEclipticalRectangularCoordinates(): HeliocentricEclipticalRectangularCoordinates
    {
        $BRad = deg2rad($this->latitude);
        $LRad = deg2rad($this->longitude);

        $x = $this->radiusVector * cos($BRad) * cos($LRad);
        $y = $this->radiusVector * cos($BRad) * sin($LRad);
        $z = $this->radiusVector * sin($BRad);

        return new HeliocentricEclipticalRectangularCoordinates($x, $y, $z);
    }

    // TODO
    public function getHeliocentricEquatorialRectangularCoordinates(): HeliocentricEquatorialRectangularCoordinates
    {
        return new HeliocentricEquatorialRectangularCoordinates(0, 0, 0);
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
