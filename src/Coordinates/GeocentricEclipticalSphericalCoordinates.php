<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Calculations\EarthCalc;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

class GeocentricEclipticalSphericalCoordinates
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

    // TODO
    public function getGeocentricEquatorialRectangularCoordinates(): GeocentricEquatorialRectangularCoordinates
    {
        return new GeocentricEquatorialRectangularCoordinates(0, 0, 0);
    }

    public function getGeocentricEquatorialSphericalCoordinates(float $T): GeocentricEquatorialSphericalCoordinates
    {
        $eps = EarthCalc::getTrueObliquityOfEcliptic($T);

        $epsRad = deg2rad($eps);
        $latRad = deg2rad($this->latitude);
        $lonRad = deg2rad($this->longitude);

        // Meeus 13.3
        $rightAscension = atan2(
            sin($lonRad) * cos($epsRad) - (sin($latRad) / cos($latRad)) * sin($epsRad), cos($lonRad)
        );
        $rightAscension = rad2deg($rightAscension);
        $rightAscension = AngleUtil::normalizeAngle($rightAscension);

        // Meeus 13.4
        $declination = asin(sin($latRad) * cos($epsRad) + cos($latRad) * sin($epsRad) * sin($lonRad));
        $declination = rad2deg($declination);

        return new GeocentricEquatorialSphericalCoordinates($rightAscension, $declination, $this->radiusVector);
    }

    // TODO
    public function getLocalHorizontalCoordinates(Location $location, TimeOfInterest $toi): LocalHorizontalCoordinates
    {
        return new LocalHorizontalCoordinates(0, 0);
    }
}
