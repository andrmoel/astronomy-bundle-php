<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Calculations\CoordinateTransformations;
use Andrmoel\AstronomyBundle\Location;

class HeliocentricEclipticalSphericalCoordinates extends AbstractEclipticalSphericalCoordinates
{
    public function getHeliocentricEclipticalRectangularCoordinates(): HeliocentricEclipticalRectangularCoordinates
    {
        $coord = CoordinateTransformations::spherical2rectangular(
            $this->longitude,
            $this->latitude,
            $this->radiusVector
        );

        return new HeliocentricEclipticalRectangularCoordinates($coord[0], $coord[1], $coord[2]);
    }

    public function getHeliocentricEquatorialRectangularCoordinates(
        float $T
    ): HeliocentricEquatorialRectangularCoordinates
    {
        return $this
            ->getHeliocentricEquatorialSphericalCoordinates($T)
            ->getHeliocentricEquatorialRectangularCoordinates();
    }

    public function getHeliocentricEquatorialSphericalCoordinates(float $T): HeliocentricEquatorialSphericalCoordinates
    {
        $coord = CoordinateTransformations::eclipticalSpherical2equatorialSpherical(
            $this->longitude,
            $this->latitude,
            $this->radiusVector,
            $T
        );

        return new HeliocentricEquatorialSphericalCoordinates($coord[0], $coord[1], $coord[2]);
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
