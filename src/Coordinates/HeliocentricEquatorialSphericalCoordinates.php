<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Calculations\CoordinateTransformations;
use Andrmoel\AstronomyBundle\Location;

class HeliocentricEquatorialSphericalCoordinates extends AbstractEquatorialSphericalCoordinates
{
    public function getHeliocentricEclipticalRectangularCoordinates(
        float $T
    ): HeliocentricEclipticalRectangularCoordinates
    {
        return $this
            ->getHeliocentricEclipticalSphericalCoordinates($T)
            ->getHeliocentricEclipticalRectangularCoordinates();
    }

    public function getHeliocentricEclipticalSphericalCoordinates(float $T): HeliocentricEclipticalSphericalCoordinates
    {
        $coord = CoordinateTransformations::equatorialSpherical2eclipticalSpherical(
            $this->rightAscension,
            $this->declination,
            $this->radiusVector,
            $T
        );

        return new HeliocentricEclipticalSphericalCoordinates($coord[0], $coord[1], $coord[2]);
    }

    public function getHeliocentricEquatorialRectangularCoordinates(): HeliocentricEquatorialRectangularCoordinates
    {
        $coord = CoordinateTransformations::spherical2rectangular(
            $this->rightAscension,
            $this->declination,
            $this->radiusVector
        );

        return new HeliocentricEquatorialRectangularCoordinates($coord[0], $coord[1], $coord[2]);
    }

    public function getGeocentricEclipticalRectangularCoordinates(float $T): GeocentricEclipticalRectangularCoordinates
    {
        return $this
            ->getHeliocentricEclipticalSphericalCoordinates($T)
            ->getGeocentricEclipticalRectangularCoordinates($T);
    }

    public function getGeocentricEclipticalSphericalCoordinates(float $T): GeocentricEclipticalSphericalCoordinates
    {
        return $this
            ->getHeliocentricEclipticalSphericalCoordinates($T)
            ->getGeocentricEclipticalSphericalCoordinates($T);
    }

    public function getGeocentricEquatorialRectangularCoordinates(float $T): GeocentricEquatorialRectangularCoordinates
    {
        return $this
            ->getHeliocentricEclipticalSphericalCoordinates($T)
            ->getGeocentricEquatorialRectangularCoordinates($T);
    }

    public function getGeocentricEquatorialSphericalCoordinates(float $T): GeocentricEquatorialSphericalCoordinates
    {
        return $this
            ->getHeliocentricEclipticalSphericalCoordinates($T)
            ->getGeocentricEquatorialSphericalCoordinates($T);
    }

    public function getLocalHorizontalCoordinates(Location $location, $T): LocalHorizontalCoordinates
    {
        return $this
            ->getHeliocentricEclipticalSphericalCoordinates($T)
            ->getLocalHorizontalCoordinates($location, $T);
    }
}
