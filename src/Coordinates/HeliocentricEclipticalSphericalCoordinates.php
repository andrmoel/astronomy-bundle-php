<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Earth;
use Andrmoel\AstronomyBundle\TimeOfInterest;

class HeliocentricEclipticalSphericalCoordinates
{
    private $L = 0;
    private $B = 0;
    private $R = 0;

    public function __construct(float $longitude, float $latitude, float $radialVector)
    {
        $this->L = $longitude;
        $this->B = $latitude;
        $this->R = $radialVector;
    }

    public function getLongitude(): float
    {
        return $this->L;
    }

    public function getLatitude(): float
    {
        return $this->B;
    }

    public function getRadiusVector(): float
    {
        return $this->R;
    }

    public function getHeliocentricEclipticalRectangularCoordinates(): HeliocentricEclipticalRectangularCoordinates
    {
        $LRad = deg2rad($this->L);
        $BRad = deg2rad($this->B);

        $X = $this->R * cos($BRad) * cos($LRad);
        $Y = $this->R * cos($BRad) * sin($LRad);
        $Z = $this->R * sin($BRad);

        return new HeliocentricEclipticalRectangularCoordinates($X, $Y, $Z);
    }

    public function getGeocentricEclipticalRectangularCoordinates(
        TimeOfInterest $toi
    ): GeocentricEclipticalRectangularCoordinates
    {
        $LRad = deg2rad($this->L);
        $BRad = deg2rad($this->B);
        $R = $this->R;

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
