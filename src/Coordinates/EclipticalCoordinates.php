<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

class EclipticalCoordinates
{
    private $longitude = 0.0;
    private $latitude = 0.0;
    private $distance = 0.0;

    public function __construct(float $latitude, float $longitude, float $distance = 0.0)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->distance = $distance;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function getDistance(): float
    {
        return $this->distance;
    }

    public function getEquatorialCoordinates(float $obliquityOfEcliptic): EquatorialCoordinates
    {
        $eps = deg2rad($obliquityOfEcliptic);
        $lat = deg2rad($this->latitude);
        $lon = deg2rad($this->longitude);

        // Meeus 13.3
        $rightAscension = atan2(sin($lon) * cos($eps) - (sin($lat) / cos($lat)) * sin($eps), cos($lon));
        $rightAscension = rad2deg($rightAscension);

        // Meeus 13.4
        $declination = asin(sin($lat) * cos($eps) + cos($lat) * sin($eps) * sin($lon));
        $declination = rad2deg($declination);

        return new EquatorialCoordinates($rightAscension, $declination);
    }
}
