<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Location;

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

    public function getGeocentricEclipticalRectangularCoordinates(float $T): GeocentricEclipticalRectangularCoordinates
    {
        return $this
            ->getHeliocentricEclipticalRectangularCoordinates()
            ->getGeocentricEclipticalRectangularCoordinates($T);
    }

    public function getGeocentricEclipticalSphericalCoordinates(float $T): GeocentricEclipticalSphericalCoordinates
    {
        return $this
            ->getHeliocentricEclipticalRectangularCoordinates()
            ->getGeocentricEclipticalSphericalCoordinates($T);
    }

    public function getGeocentricEquatorialRectangularCoordinates(float $T): GeocentricEquatorialRectangularCoordinates
    {
        return $this
            ->getHeliocentricEclipticalRectangularCoordinates()
            ->getGeocentricEquatorialRectangularCoordinates($T);
    }

    public function getGeocentricEquatorialSphericalCoordinates(float $T): GeocentricEquatorialSphericalCoordinates
    {
        return $this
            ->getHeliocentricEclipticalRectangularCoordinates()
            ->getGeocentricEquatorialSphericalCoordinates($T);
    }

    public function getLocalHorizontalCoordinates(Location $location, $T): LocalHorizontalCoordinates
    {
        return $this
            ->getHeliocentricEclipticalRectangularCoordinates()
            ->getLocalHorizontalCoordinates($location, $T);
    }
}
