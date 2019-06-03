<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Calculations\CoordinateTransformations;
use Andrmoel\AstronomyBundle\Location;

class GeocentricEclipticalSphericalCoordinates extends AbstractEclipticalSphericalCoordinates
{
    public function getGeocentricEclipticalRectangularCoordinates(): GeocentricEclipticalRectangularCoordinates
    {
        $coord = CoordinateTransformations::spherical2rectangular(
            $this->longitude,
            $this->latitude,
            $this->radiusVector
        );

        return new GeocentricEclipticalRectangularCoordinates($coord[0], $coord[1], $coord[2]);
    }

    public function getGeocentricEquatorialRectangularCoordinates(float $T): GeocentricEquatorialRectangularCoordinates
    {
        return $this
            ->getGeocentricEquatorialSphericalCoordinates($T)
            ->getGeocentricEquatorialRectangularCoordinates();
    }

    public function getGeocentricEquatorialSphericalCoordinates(float $T): GeocentricEquatorialSphericalCoordinates
    {
        $coord = CoordinateTransformations::eclipticalSpherical2equatorialSpherical(
            $this->longitude,
            $this->latitude,
            $this->radiusVector,
            $T
        );

        return new GeocentricEquatorialSphericalCoordinates($coord[0], $coord[1], $coord[2]);
    }

    public function getLocalHorizontalCoordinates(Location $location, float $T): LocalHorizontalCoordinates
    {
        return $this->getGeocentricEquatorialSphericalCoordinates($T)
            ->getLocalHorizontalCoordinates($location, $T);
    }
}
