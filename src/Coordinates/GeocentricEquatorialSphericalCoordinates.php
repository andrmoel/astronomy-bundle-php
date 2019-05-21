<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Calculations\EarthCalc;
use Andrmoel\AstronomyBundle\Calculations\TimeCalc;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

class GeocentricEquatorialSphericalCoordinates
{
    private $rightAscension = 0;
    private $declination = 0;
    private $radiusVector = 0;

    public function __construct(float $rightAscension, float $declination, float $radiusVector = 0.0)
    {
        $this->rightAscension = $rightAscension;
        $this->declination = $declination;
        $this->radiusVector = $radiusVector;
    }

    public function getRightAscension(): float
    {
        return $this->rightAscension;
    }

    public function getDeclination(): float
    {
        return $this->declination;
    }

    public function getRadiusVector(): float
    {
        return $this->radiusVector;
    }

    public function getGeocentricEclipticalSphericalCoordinates(float $T): GeocentricEclipticalSphericalCoordinates
    {
        $eps = EarthCalc::getTrueObliquityOfEcliptic($T);
        $raRad = deg2rad($this->rightAscension);
        $dRad = deg2rad($this->declination);
        $epsRad = deg2rad($eps);

        // Meeus 13.1
        $lonRad = atan2((sin($raRad) * cos($epsRad) + tan($dRad) * sin($epsRad)), cos($raRad));
        $lon = AngleUtil::normalizeAngle(rad2deg($lonRad)); // TODO +180 ???

        // Meeus 13.2
        $latRad = asin(sin($dRad) * cos($epsRad) - cos($dRad) * sin($epsRad) * sin($raRad));
        $lat = rad2deg($latRad);

        return new GeocentricEclipticalSphericalCoordinates($lat, $lon, $this->radiusVector);
    }

    public function getGeocentricEquatorialRectangularCoordinates(): GeocentricEquatorialRectangularCoordinates
    {
        $raRad = deg2rad($this->rightAscension);
        $dRad = deg2rad($this->declination);
        $R = $this->radiusVector;

        $X = cos($dRad)  * cos($raRad) * $R;
        $Y = cos($dRad) * sin($raRad) * $R;
        $Z = sin($dRad) * $R;

        return new GeocentricEquatorialRectangularCoordinates($X, $Y, $Z);
    }

    public function getLocalHorizontalCoordinates(Location $location, float $T): LocalHorizontalCoordinates
    {
        $lat = $location->getLatitude();
        $L = $location->getLongitudePositiveWest();
        $GAST = TimeCalc::getGreenwichApparentSiderealTime($T);

        $dRad = deg2rad($this->declination);
        $latRad = deg2rad($lat);

        // Calculate hour angle // TODO extra function for hour angle
        $H = $GAST - $L - $this->rightAscension;
        $H = AngleUtil::normalizeAngle($H);
        $HRad = deg2rad($H);

        // Meeus 13.5
        $azimuth = atan(sin($HRad) / (cos($HRad) * sin($latRad) - tan($dRad) * cos($latRad)));
        $azimuth = rad2deg($azimuth);
        $azimuth = AngleUtil::normalizeAngle($azimuth);

        // Meeus 13.6
        $altitude = asin(sin($latRad) * sin($dRad) + cos($latRad) * cos($dRad) * cos($HRad));
        $altitude = rad2deg($altitude);

        return new LocalHorizontalCoordinates($azimuth, $altitude);
    }
}
