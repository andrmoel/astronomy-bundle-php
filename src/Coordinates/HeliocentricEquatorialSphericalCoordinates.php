<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

class HeliocentricEquatorialSphericalCoordinates
{
    private $rightAscension = 0;
    private $declination = 0;
    private $radiusVector = 0;

    public function __construct(float $rightAscension, float $declination, float $radiusVector = 0.0)
    {
        $this->rightAscension = $rightAscension;
        $this->declination = $declination;
        $this->radiusVector = $radiusVector;
    }

    public function getRightAscension(): float
    {
        return $this->rightAscension;
    }

    public function getDeclination(): float
    {
        return $this->declination;
    }

    public function getRadiusVector(): float
    {
        return $this->radiusVector;
    }

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

    // TODO Test!
    public function getHeliocentricEquatorialRectangularCoordinates(): HeliocentricEquatorialRectangularCoordinates
    {
        $raRad = deg2rad($this->rightAscension);
        $dRad = deg2rad($this->declination);
        $R = $this->radiusVector;

        $X = cos($dRad) * cos($raRad) * $R;
        $Y = cos($dRad) * sin($raRad) * $R;
        $Z = sin($dRad) * $R;

        return new HeliocentricEquatorialRectangularCoordinates($X, $Y, $Z);
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
