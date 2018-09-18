<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Location;

class LocalHorizontalCoordinates
{
    private $azimuth = 0;
    private $altitude = 0;


    public function __construct(float $azimuth, float $altitude)
    {
        $this->azimuth = $azimuth;
        $this->altitude = $altitude;
    }


    public function getAzimuth(): float
    {
        return $this->azimuth;
    }


    public function getAltitude(): float
    {
        return $this->altitude;
    }

    /**
     * TODO NOr working MEEUS 94
     * @param Location $location
     * @return EquatorialCoordinates
     */
    public function getEquatorialCoordinates(Location $location): EquatorialCoordinates
    {
        $latRad = $location->getLatitudeRad();

        // Calculate right ascension and declination
        $A = deg2rad($this->azimuth) - 180;
        $h = deg2rad($this->altitude);

        // Meeus 13
        $rightAscension = atan(sin($A) / (cos($A) * sin($latRad) + tan($h) * cos($latRad)));
        $rightAscension = rad2deg($rightAscension);
        $declination = asin(sin($latRad) * sin($h) - cos($latRad) * cos($h) * cos($A));
        $declination = rad2deg($declination);

        return new EquatorialCoordinates($rightAscension, $declination);
    }
}
