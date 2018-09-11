<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Location;

class LocalHorizontalCoordinates extends Coordinates
{
    private $azimuth = 0;
    private $altitude = 0;


    public function __construct(float $azimuth, float $altitude)
    {
        parent::__construct();

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
        $azimuth = deg2rad($this->azimuth);
        $altitude = deg2rad($this->altitude);

        $rightAscension = atan(sin($azimuth) / (cos($azimuth) * sin($latRad) + tan($altitude) * cos($latRad)));
        $rightAscension = rad2deg($rightAscension);
        $declination = asin(sin($latRad) * sin($altitude) - cos($latRad) * cos($altitude) * cos($azimuth));
        $declination = rad2deg($declination);

        return new EquatorialCoordinates($rightAscension, $declination);
    }
}
