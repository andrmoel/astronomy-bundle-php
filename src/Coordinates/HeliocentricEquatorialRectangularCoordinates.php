<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

class HeliocentricEquatorialRectangularCoordinates extends AbstractRectangularCoordinates
{
    // TODO
    public function getHeliocentricEclipticalRectangularCoordinates(): HeliocentricEclipticalRectangularCoordinates
    {
        return new HeliocentricEclipticalRectangularCoordinates(0, 0, 0);
    }

    // TODO
    public function getHeliocentricEclipticalSphericalCoordinates(): HeliocentricEclipticalSphericalCoordinates
    {
        return new HeliocentricEclipticalSphericalCoordinates(0, 0, 0);
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
