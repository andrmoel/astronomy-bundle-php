<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Calculations\EarthCalc;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

class GeocentricEclipticalSphericalCoordinates
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

    public function getGeocentricEclipticalRectangularCoordinates(): GeocentricEclipticalRectangularCoordinates
    {
        $lonRad = deg2rad($this->longitude);
        $latRad = deg2rad($this->latitude);

        $X = $this->radiusVector * cos($latRad) * cos($lonRad);
        $Y = $this->radiusVector * cos($latRad) * sin($lonRad);
        $Z = $this->radiusVector * sin($latRad);

        return new GeocentricEclipticalRectangularCoordinates($X, $Y, $Z);
    }

    public function getGeocentricEquatorialCoordinates(TimeOfInterest $toi): GeocentricEquatorialCoordinates
    {
        $T = $toi->getJulianCenturiesFromJ2000();

        $eps = EarthCalc::getMeanObliquityOfEcliptic($T);

        $epsRad = deg2rad($eps);
        $lonRad = deg2rad($this->longitude);
        $latRad = deg2rad($this->latitude);

        // Meeus 13.3
        $rightAscension = atan2(
            sin($lonRad) * cos($epsRad) - (sin($latRad) / cos($latRad)) * sin($epsRad), cos($lonRad)
        );
        $rightAscension = rad2deg($rightAscension);
        $rightAscension = AngleUtil::normalizeAngle($rightAscension);

        // Meeus 13.4
        $declination = asin(sin($latRad) * cos($epsRad) + cos($latRad) * sin($epsRad) * sin($lonRad));
        $declination = rad2deg($declination);

        return new GeocentricEquatorialCoordinates($rightAscension, $declination, $this->radiusVector);
    }
}
