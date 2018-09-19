<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

class GeocentricEclipticalSphericalCoordinates
{
    private $longitude = 0.0;
    private $latitude = 0.0;
    private $radiusVector = 0.0;

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

    public function getGeocentricEquatorialCoordinates(float $obliquityOfEcliptic): GeocentricEquatorialCoordinates
    {
        $eps = deg2rad($obliquityOfEcliptic);
        $lon = deg2rad($this->longitude);
        $lat = deg2rad($this->latitude);

        // Meeus 13.3
        $rightAscension = atan2(sin($lon) * cos($eps) - (sin($lat) / cos($lat)) * sin($eps), cos($lon));
        $rightAscension = rad2deg($rightAscension);

        // Meeus 13.4
        $declination = asin(sin($lat) * cos($eps) + cos($lat) * sin($eps) * sin($lon));
        $declination = rad2deg($declination);

        return new GeocentricEquatorialCoordinates($rightAscension, $declination, $this->radiusVector);
    }
}
