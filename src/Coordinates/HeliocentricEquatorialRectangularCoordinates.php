<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Calculations\CoordinateTransformations;
use Andrmoel\AstronomyBundle\Location;

class HeliocentricEquatorialRectangularCoordinates extends AbstractRectangularCoordinates
{
    public function getHeliocentricEclipticalRectangularCoordinates(float $T): HeliocentricEclipticalRectangularCoordinates
    {
        return $this
            ->getHeliocentricEquatorialSphericalCoordinates()
            ->getHeliocentricEclipticalRectangularCoordinates($T);
    }

    public function getHeliocentricEclipticalSphericalCoordinates(float $T): HeliocentricEclipticalSphericalCoordinates
    {
        return $this
            ->getHeliocentricEquatorialSphericalCoordinates()
            ->getHeliocentricEclipticalSphericalCoordinates($T);
    }

    public function getHeliocentricEquatorialSphericalCoordinates(): HeliocentricEquatorialSphericalCoordinates
    {
        $coord = CoordinateTransformations::rectangular2spherical($this->x, $this->y, $this->z);

        return new HeliocentricEquatorialSphericalCoordinates($coord[0], $coord[1], $coord[2]);
    }

    public function getGeocentricEclipticalRectangularCoordinates(float $T): GeocentricEclipticalRectangularCoordinates
    {
        return $this
            ->getHeliocentricEclipticalRectangularCoordinates($T)
            ->getGeocentricEclipticalRectangularCoordinates($T);
    }

    public function getGeocentricEclipticalSphericalCoordinates(float $T): GeocentricEclipticalSphericalCoordinates
    {
        return $this
            ->getHeliocentricEclipticalRectangularCoordinates($T)
            ->getGeocentricEclipticalSphericalCoordinates($T);
    }

    public function getGeocentricEquatorialRectangularCoordinates(float $T): GeocentricEquatorialRectangularCoordinates
    {
        return $this
            ->getHeliocentricEclipticalRectangularCoordinates($T)
            ->getGeocentricEquatorialRectangularCoordinates($T);
    }

    public function getGeocentricEquatorialSphericalCoordinates(float $T): GeocentricEquatorialSphericalCoordinates
    {
        return $this
            ->getHeliocentricEclipticalRectangularCoordinates($T)
            ->getGeocentricEquatorialSphericalCoordinates($T);
    }

    public function getLocalHorizontalCoordinates(Location $location, $T): LocalHorizontalCoordinates
    {
        return $this
            ->getHeliocentricEclipticalRectangularCoordinates($T)
            ->getLocalHorizontalCoordinates($location, $T);
    }
}
