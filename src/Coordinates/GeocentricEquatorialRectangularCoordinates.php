<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Calculations\CoordinateTransformations;
use Andrmoel\AstronomyBundle\Location;

class GeocentricEquatorialRectangularCoordinates extends AbstractRectangularCoordinates
{
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
        $coord = CoordinateTransformations::rectangular2spherical($this->x, $this->y, $this->z);

        return new GeocentricEquatorialSphericalCoordinates($coord[0], $coord[1], $coord[2]);
    }

    public function getLocalHorizontalCoordinates(Location $location, float $T): LocalHorizontalCoordinates
    {
        return $this
            ->getGeocentricEquatorialSphericalCoordinates()
            ->getLocalHorizontalCoordinates($location, $T);
    }
}
