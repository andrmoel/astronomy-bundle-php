<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Calculations\EarthCalc;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

class HeliocentricEclipticalSphericalCoordinates
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

    public function getHeliocentricEclipticalRectangularCoordinates(): HeliocentricEclipticalRectangularCoordinates
    {
        $BRad = deg2rad($this->latitude);
        $LRad = deg2rad($this->longitude);

        $x = $this->radiusVector * cos($BRad) * cos($LRad);
        $y = $this->radiusVector * cos($BRad) * sin($LRad);
        $z = $this->radiusVector * sin($BRad);

        return new HeliocentricEclipticalRectangularCoordinates($x, $y, $z);
    }

    public function getHeliocentricEquatorialRectangularCoordinates(float $T): HeliocentricEquatorialRectangularCoordinates
    {
        return $this
            ->getHeliocentricEquatorialSphericalCoordinates($T)
            ->getHeliocentricEquatorialRectangularCoordinates();
    }

    public function getHeliocentricEquatorialSphericalCoordinates(float $T): HeliocentricEquatorialSphericalCoordinates
    {
        $eps = EarthCalc::getTrueObliquityOfEcliptic($T);

        $epsRad = deg2rad($eps);
        $latRad = deg2rad($this->latitude);
        $lonRad = deg2rad($this->longitude);

        // Meeus 13.3
        $rightAscensionRad = atan2(
            sin($lonRad) * cos($epsRad) - (sin($latRad) / cos($latRad)) * sin($epsRad),
            cos($lonRad)
        );
        $rightAscension = AngleUtil::normalizeAngle(rad2deg($rightAscensionRad));

        // Meeus 13.4
        $declinationRad = asin(sin($latRad) * cos($epsRad) + cos($latRad) * sin($epsRad) * sin($lonRad));
        $declination = rad2deg($declinationRad);

        return new HeliocentricEquatorialSphericalCoordinates($rightAscension, $declination, $this->radiusVector);
    }

    public function getGeocentricEclipticalRectangularCoordinates(float $T): GeocentricEclipticalRectangularCoordinates
    {
        return $this
            ->getHeliocentricEclipticalRectangularCoordinates()
            ->getGeocentricEclipticalRectangularCoordinates($T);
    }

    public function getGeocentricEclipticalSphericalCoordinates(float $T): GeocentricEclipticalSphericalCoordinates
    {
        return $this
            ->getHeliocentricEclipticalRectangularCoordinates()
            ->getGeocentricEclipticalSphericalCoordinates($T);
    }

    public function getGeocentricEquatorialRectangularCoordinates(float $T): GeocentricEquatorialRectangularCoordinates
    {
        return $this
            ->getHeliocentricEclipticalRectangularCoordinates()
            ->getGeocentricEquatorialRectangularCoordinates($T);
    }

    public function getGeocentricEquatorialSphericalCoordinates(float $T): GeocentricEquatorialSphericalCoordinates
    {
        return $this
            ->getHeliocentricEclipticalRectangularCoordinates()
            ->getGeocentricEquatorialSphericalCoordinates($T);
    }

    public function getLocalHorizontalCoordinates(Location $location, $T): LocalHorizontalCoordinates
    {
        return $this
            ->getHeliocentricEclipticalRectangularCoordinates()
            ->getLocalHorizontalCoordinates($location, $T);
    }
}
