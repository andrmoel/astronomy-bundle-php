<?php

namespace Andrmoel\AstronomyBundle;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Earth;

class Location
{
    private $latitude = 0.0;
    private $longitude = 0.0;
    private $elevation = 0.0;

    private $latitudeRad;
    private $longitudeRad;
    private $rhoSinOs;
    private $rhoCosOs;


    public function __construct(float $latitude = 0.0, float $longitude = 0.0, float $elevation = 0.0)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->latitudeRad = deg2rad($latitude);
        $this->longitudeRad = deg2rad($longitude);
        $this->elevation = $elevation >= 0.0 ? $elevation : 0.0;

        $this->calculateGeocentricPosition();
    }


    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
        $this->latitudeRad = deg2rad($latitude);

        $this->calculateGeocentricPosition();
    }


    public function getLatitude(): float
    {
        return $this->latitude;
    }


    public function getLatitudeRad(): float
    {
        return $this->latitudeRad;
    }


    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
        $this->longitudeRad = deg2rad($longitude);

        $this->calculateGeocentricPosition();
    }


    public function getLongitude(): float
    {
        return $this->longitude;
    }


    public function getLongitudePositiveWest(): float
    {
        return -1 * $this->longitude;
    }


    public function getLongitudeRad(): float
    {
        return $this->longitudeRad;
    }


    public function getLongitudePositiveWestRad(): float
    {
        return -1 * $this->longitudeRad;
    }


    public function setElevation(float $elevation): void
    {
        $this->elevation = $elevation >= 0.0 ? $elevation : 0.0;

        $this->calculateGeocentricPosition();
    }


    public function getElevation(): float
    {
        return $this->elevation;
    }


    public function getRhoSinOs(): float
    {
        return $this->rhoSinOs;
    }


    public function getRhoCosOs(): float
    {
        return $this->rhoCosOs;
    }


    // TODO Gleiche Formel wird auch in SoFi / MoFi Berechung benutzt
    private function calculateGeocentricPosition(): void
    {
        // Get the observer's geocentric position
        $tmp = atan(Earth::EARTH_AXIS_RATIO * tan($this->latitudeRad));
        $this->rhoSinOs = Earth::EARTH_AXIS_RATIO * sin($tmp) + $this->elevation * sin($this->latitudeRad) / Earth::RADIUS;
        $this->rhoCosOs = cos($tmp) + $this->elevation * sin($this->latitudeRad) / Earth::RADIUS;
    }
}
