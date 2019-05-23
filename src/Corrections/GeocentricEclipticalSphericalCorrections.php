<?php

namespace Andrmoel\AstronomyBundle\Corrections;

use Andrmoel\AstronomyBundle\Calculations\EarthCalc;
use Andrmoel\AstronomyBundle\Calculations\SunCalc;
use Andrmoel\AstronomyBundle\Constants;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalSphericalCoordinates;

class GeocentricEclipticalSphericalCorrections
{
    public static function correctEffectOfNutation(
        GeocentricEclipticalSphericalCoordinates $coord,
        float $T
    ): GeocentricEclipticalSphericalCoordinates
    {
        $lat = $coord->getLatitude();
        $lon = $coord->getLongitude();
        $r = $coord->getRadiusVector();

        $dPhi = EarthCalc::getNutationInLongitude($T);
        $lon += $dPhi;

        return new GeocentricEclipticalSphericalCoordinates($lat, $lon, $r);
    }

    public static function correctEffectOfAberration(
        GeocentricEclipticalSphericalCoordinates $coord,
        float $T
    ): GeocentricEclipticalSphericalCoordinates
    {
        $lat = $coord->getLatitude();
        $lon = $coord->getLongitude();
        $r = $coord->getRadiusVector();

        $k = Constants::CONSTANT_OF_ABERRATION;
        $e = EarthCalc::getEccentricity($T);
        $pi = EarthCalc::getLongitudeOfPerihelionOfOrbit($T);
        $o = SunCalc::getTrueLongitude($T);

        $lonRad = deg2rad($lon);
        $latRad = deg2rad($lat);
        $piRad = deg2rad($pi);
        $oRad = deg2rad($o);

        // Meeus 23.2
        $dLat = -$k * sin($latRad) * (sin($oRad - $lonRad) - $e * sin($piRad - $lonRad));
        $dLon = (-$k * cos($oRad - $lonRad) + $e * $k * cos($piRad - $lonRad)) / cos($latRad);

        $lat += $dLat;
        $lon += $dLon;

        return new GeocentricEclipticalSphericalCoordinates($lat, $lon, $r);
    }
}
