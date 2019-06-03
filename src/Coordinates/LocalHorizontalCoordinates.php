<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Calculations\TimeCalc;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

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

    public function getGeocentricEclipticalRectangularCoordinates(
        Location $location,
        float $T
    ): GeocentricEclipticalRectangularCoordinates
    {
        return $this
            ->getGeocentricEquatorialSphericalCoordinates($location, $T)
            ->getGeocentricEclipticalRectangularCoordinates($T);
    }

    public function getGeocentricEclipticalSphericalCoordinates(
        Location $location,
        float $T
    ): GeocentricEclipticalSphericalCoordinates
    {
        return $this
            ->getGeocentricEquatorialSphericalCoordinates($location, $T)
            ->getGeocentricEclipticalSphericalCoordinates($T);
    }

    public function getGeocentricEquatorialRectangularCoordinates(
        Location $location,
        float $T
    ): GeocentricEquatorialRectangularCoordinates
    {
        return $this
            ->getGeocentricEquatorialSphericalCoordinates($location, $T)
            ->getGeocentricEquatorialRectangularCoordinates();
    }

    public function getGeocentricEquatorialSphericalCoordinates(
        Location $location,
        float $T
    ): GeocentricEquatorialSphericalCoordinates
    {
        $lat = $location->getLatitude();
        $L = $location->getLongitudePositiveWest();
        $GAST = TimeCalc::getGreenwichApparentSiderealTime($T);

        $latRad = deg2rad($lat);
        $ARad = deg2rad($this->azimuth);
        $hRad = deg2rad($this->altitude);

        // Meeus 13
        $H = atan(sin($ARad) / (cos($ARad) * sin($latRad) + tan($hRad) * cos($latRad)));
        $H = rad2deg($H);

        $rightAscension = $GAST - $L - $H;
        $rightAscension = AngleUtil::normalizeAngle($rightAscension);

        $declination = asin(sin($latRad) * sin($hRad) - cos($latRad) * cos($hRad) * cos($ARad));
        $declination = rad2deg($declination);

        return new GeocentricEquatorialSphericalCoordinates($rightAscension, $declination);
    }
}
