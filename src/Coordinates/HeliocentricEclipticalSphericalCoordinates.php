<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Earth;
use Andrmoel\AstronomyBundle\TimeOfInterest;

class HeliocentricEclipticalSphericalCoordinates
{

    protected $longitude = 0.0;
    protected $latitude = 0.0;
    protected $radiusVector = 0.0;

    public function __construct(float $longitude, float $latitude, float $radiusVector = 0.0)
    {
        $this->longitude = $longitude;
        $this->latitude = $latitude;
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

        $X = $this->radiusVector * cos($BRad) * cos($LRad);
        $Y = $this->radiusVector * cos($BRad) * sin($LRad);
        $Z = $this->radiusVector * sin($BRad);

        return new HeliocentricEclipticalRectangularCoordinates($X, $Y, $Z);
    }

    public function getGeocentricEclipticalRectangularCoordinates(
        TimeOfInterest $toi
    ): GeocentricEclipticalRectangularCoordinates
    {
        $LRad = deg2rad($this->longitude);
        $BRad = deg2rad($this->latitude);
        $R = $this->radiusVector;

        // Heliocentric coordinates of earth
        $earth = new Earth($toi);
        $hcEclSphCoordinatesEarth = $earth->getHeliocentricEclipticalSphericalCoordinates();
        $L0 = $hcEclSphCoordinatesEarth->getLongitude();
        $L0Rad = deg2rad($L0);
        $B0 = $hcEclSphCoordinatesEarth->getLatitude();
        $B0Rad = deg2rad($B0);
        $R0 = $hcEclSphCoordinatesEarth->getRadiusVector();

        // Meeus 33.1
        $X = $R * cos($BRad) * cos($LRad) - $R0 * cos($B0Rad) * cos($L0Rad);
        $Y = $R * cos($BRad) * sin($LRad) - $R0 * cos($B0Rad) * sin($L0Rad);
        $Z = $R * sin($BRad) - $R0 * sin($B0Rad);

        return new GeocentricEclipticalRectangularCoordinates($X, $Y, $Z);
    }

    public function getGeocentricEclipticalSphericalCoordinates(
        TimeOfInterest $toi
    ): GeocentricEclipticalSphericalCoordinates
    {
        return $this->getGeocentricEclipticalRectangularCoordinates($toi)
            ->getGeocentricEclipticalSphericalCoordinates();
    }
}
