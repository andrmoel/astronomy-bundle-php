<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Calculations\CoordinateTransformations;

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
