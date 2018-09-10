<?php

namespace App\Util\Astro;

class Observer
{
    private $lat = 0.0;
    private $lon = 0.0;
    private $elevation = 0.0;

    private $latRad;
    private $lonRad;
    private $rhoSinOs;
    private $rhoCosOs;

    public function __construct($lat, $lon, $elevation = 0.0)
    {
        $this->lat = $lat;
        $this->lon = $lon;
        $this->latRad = deg2rad($lat);
        $this->lonRad = -1 * deg2rad($lon);
        $this->elevation = $elevation >= 0.0 ? $elevation : $elevation;

        $this->calculateGeocentricPosition();
    }

    public function setLatitude($lat)
    {
        $this->lat = $lat;
        $this->latRad = deg2rad($lat);

        $this->calculateGeocentricPosition();
    }

    public function setLongitude($lon)
    {
        $this->lon = $lon;
        $this->lonRad = -1 * deg2rad($lon);

        $this->calculateGeocentricPosition();
    }

    public function setElevation($elevation)
    {
        $this->elevation = $elevation;

        $this->calculateGeocentricPosition();
    }

    private function calculateGeocentricPosition()
    {
        $tmp = atan(0.996647189335 * tan($this->latRad));
        $this->rhoSinOs = 0.996647189335 * sin($tmp) + $this->elevation * sin($this->latRad) / 6378137.0;
        $this->rhoCosOs = cos($tmp) + $this->elevation * sin($this->latRad) / 6378137.0;
    }
}
