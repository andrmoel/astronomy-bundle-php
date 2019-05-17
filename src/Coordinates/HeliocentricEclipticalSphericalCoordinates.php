<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Earth;
use Andrmoel\AstronomyBundle\TimeOfInterest;

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
        $LRad = deg2rad($this->longitude);
        $BRad = deg2rad($this->latitude);

        $x = $this->radiusVector * cos($BRad) * cos($LRad);
        $y = $this->radiusVector * cos($BRad) * sin($LRad);
        $z = $this->radiusVector * sin($BRad);

        return new HeliocentricEclipticalRectangularCoordinates($x, $y, $z);
    }

    public function getHeliocentricEquatorialRectangularCoordinates(): HeliocentricEquatorialRectangularCoordinates
    {
        // TODO ...
        return new HeliocentricEquatorialRectangularCoordinates(0, 0, 0);
    }

    public function getGeocentricEclipticalSphericalCoordinates(): GeocentricEclipticalSphericalCoordinates
    {
        // TODO
        return new GeocentricEclipticalSphericalCoordinates(0, 0, 0);
    }

    public function getGeocentricEquatorialRectangularCoordinates(
        TimeOfInterest $toi
    ): GeocentricEquatorialRectangularCoordinates
    {
        $LRad = deg2rad($this->longitude);
        $BRad = deg2rad($this->latitude);
        $R = $this->radiusVector;

        // Heliocentric coordinates of earth
        $earth = new Earth($toi);
        $hcEclSphCoordinatesEarth = $earth->getHeliocentricEclipticalSphericalCoordinates();
        $L0 = $hcEclSphCoordinatesEarth->getLongitude();
        $B0 = $hcEclSphCoordinatesEarth->getLatitude();
        $R0 = $hcEclSphCoordinatesEarth->getRadiusVector();

        $B0Rad = deg2rad($B0);
        $L0Rad = deg2rad($L0);

        // Meeus 33.1
        $X = $R * cos($BRad) * cos($LRad) - $R0 * cos($B0Rad) * cos($L0Rad);
        $Y = $R * cos($BRad) * sin($LRad) - $R0 * cos($B0Rad) * sin($L0Rad);
        $Z = $R * sin($BRad) - $R0 * sin($B0Rad);

        return new GeocentricEquatorialRectangularCoordinates($X, $Y, $Z);
    }

    public function getGeocentricEquatorialSphericalCoordinates(): GeocentricEquatorialSphericalCoordinates
    {
        // TODO
        return new GeocentricEquatorialSphericalCoordinates(0, 0, 0);
    }
}
