<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Calculations\CoordinateTransformations;
use Andrmoel\AstronomyBundle\Calculations\VSOP87\EarthRectangularVSOP87;
use Andrmoel\AstronomyBundle\Calculations\VSOP87Calc;
use Andrmoel\AstronomyBundle\Location;

class HeliocentricEclipticalRectangularCoordinates extends AbstractRectangularCoordinates
{
    public function getHeliocentricEclipticalSphericalCoordinates(): HeliocentricEclipticalSphericalCoordinates
    {
        $coord = CoordinateTransformations::rectangular2spherical($this->x, $this->y, $this->z);

        return new HeliocentricEclipticalSphericalCoordinates($coord[0], $coord[1], $coord[2]);
    }

    public function getHeliocentricEquatorialRectangularCoordinates(float $T): HeliocentricEquatorialRectangularCoordinates
    {
        return $this
            ->getHeliocentricEclipticalSphericalCoordinates()
            ->getHeliocentricEquatorialRectangularCoordinates($T);
    }

    public function getHeliocentricEquatorialSphericalCoordinates(float $T): HeliocentricEquatorialSphericalCoordinates
    {
        return $this
            ->getHeliocentricEclipticalSphericalCoordinates()
            ->getHeliocentricEquatorialSphericalCoordinates($T);
    }

    public function getGeocentricEclipticalRectangularCoordinates(float $T): GeocentricEclipticalRectangularCoordinates
    {
        $t = $T / 10;

        // Heliocentric coordinates of earth
        $coefficients = VSOP87Calc::solve(EarthRectangularVSOP87::class, $t);

        $X = $this->x - $coefficients[0];
        $Y = $this->y - $coefficients[1];
        $Z = $this->z - $coefficients[2];

        return new GeocentricEclipticalRectangularCoordinates($X, $Y, $Z);
    }

    public function getGeocentricEclipticalSphericalCoordinates(float $T): GeocentricEclipticalSphericalCoordinates
    {
        return $this
            ->getGeocentricEclipticalRectangularCoordinates($T)
            ->getGeocentricEclipticalSphericalCoordinates();
    }

    public function getGeocentricEquatorialRectangularCoordinates(float $T): GeocentricEquatorialRectangularCoordinates
    {
        return $this
            ->getGeocentricEclipticalRectangularCoordinates($T)
            ->getGeocentricEquatorialRectangularCoordinates($T);
    }

    public function getGeocentricEquatorialSphericalCoordinates(float $T): GeocentricEquatorialSphericalCoordinates
    {
        return $this
            ->getGeocentricEclipticalRectangularCoordinates($T)
            ->getGeocentricEquatorialSphericalCoordinates($T);
    }

    public function getLocalHorizontalCoordinates(Location $location, $T): LocalHorizontalCoordinates
    {
        return $this
            ->getGeocentricEclipticalRectangularCoordinates($T)
            ->getLocalHorizontalCoordinates($location, $T);
    }
}
