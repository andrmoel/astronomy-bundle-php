<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Calculations\EarthCalc;
use Andrmoel\AstronomyBundle\Location;
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

    public function getGeocentricEclipticalRectangularCoordinates(): GeocentricEclipticalRectangularCoordinates
    {
        $latRad = deg2rad($this->latitude);
        $lonRad = deg2rad($this->longitude);

        $X = $this->radiusVector * cos($latRad) * cos($lonRad);
        $Y = $this->radiusVector * cos($latRad) * sin($lonRad);
        $Z = $this->radiusVector * sin($latRad);

        return new GeocentricEclipticalRectangularCoordinates($X, $Y, $Z);
    }

    public function getGeocentricEquatorialRectangularCoordinates(float $T): GeocentricEquatorialRectangularCoordinates
    {
        return $this
            ->getGeocentricEquatorialSphericalCoordinates($T)
            ->getGeocentricEquatorialRectangularCoordinates();
    }

    public function getGeocentricEquatorialSphericalCoordinates(float $T): GeocentricEquatorialSphericalCoordinates
    {
        $eps = EarthCalc::getTrueObliquityOfEcliptic($T);

        $epsRad = deg2rad($eps);
        $latRad = deg2rad($this->latitude);
        $lonRad = deg2rad($this->longitude);

        // Meeus 13.3
        $raRad = atan2(
            sin($lonRad) * cos($epsRad) - (sin($latRad) / cos($latRad)) * sin($epsRad),
            cos($lonRad)
        );
        $ra = AngleUtil::normalizeAngle(rad2deg($raRad));

        // Meeus 13.4
        $dRad = asin(sin($latRad) * cos($epsRad) + cos($latRad) * sin($epsRad) * sin($lonRad));
        $d = rad2deg($dRad);

        return new GeocentricEquatorialSphericalCoordinates($ra, $d, $this->radiusVector);
    }

    public function getLocalHorizontalCoordinates(Location $location, float $T): LocalHorizontalCoordinates
    {
        return $this->getGeocentricEquatorialSphericalCoordinates($T)
            ->getLocalHorizontalCoordinates($location, $T);
    }
}
