<?php

namespace Andrmoel\AstronomyBundle\Corrections;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Earth;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Sun;
use Andrmoel\AstronomyBundle\Calculations\EarthCalc;
use Andrmoel\AstronomyBundle\Calculations\SunCalc;
use Andrmoel\AstronomyBundle\Constants;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalSphericalCoordinates;
use Andrmoel\AstronomyBundle\TimeOfInterest;

class GeocentricEclipticalSphericalCorrections
{
    private $toi;

    public function __construct(TimeOfInterest $toi)
    {
        $this->toi = $toi;
    }

    public function correctCoordinates(
        GeocentricEclipticalSphericalCoordinates $geoEclSphCoordinates
    ): GeocentricEclipticalSphericalCoordinates
    {
        $geoEclSphCoordinates = $this->correctEffectOfNutation($geoEclSphCoordinates);
        $geoEclSphCoordinates = $this->correctEffectOfAberration($geoEclSphCoordinates);

        return $geoEclSphCoordinates;
    }

    public function correctEffectOfNutation(
        GeocentricEclipticalSphericalCoordinates $geoEclSphCoordinates
    ): GeocentricEclipticalSphericalCoordinates
    {
        $T = $this->toi->getJulianCenturiesFromJ2000();

        $lon = $geoEclSphCoordinates->getLongitude();
        $lat = $geoEclSphCoordinates->getLatitude();
        $radiusVector = $geoEclSphCoordinates->getRadiusVector();

        $dPhi = EarthCalc::getNutationInLongitude($T);

        $lon += $dPhi;

        return new GeocentricEclipticalSphericalCoordinates($lon, $lat, $radiusVector);
    }

    public function correctEffectOfAberration(
        GeocentricEclipticalSphericalCoordinates $geoEclSphCoordinates
    ): GeocentricEclipticalSphericalCoordinates
    {
        $T = $this->toi->getJulianCenturiesFromJ2000();

        $lon = $geoEclSphCoordinates->getLongitude();
        $lat = $geoEclSphCoordinates->getLatitude();
        $radiusVector = $geoEclSphCoordinates->getRadiusVector();

        $k = Constants::CONSTANT_OF_ABERRATION;
        $e = EarthCalc::getEccentricity($T);
        $pi = EarthCalc::getLongitudeOfPerihelionOfOrbit($T);
        $o = SunCalc::getTrueLongitude($T);


        $lonRad = deg2rad($lon);
        $latRad = deg2rad($lat);
        $piRad = deg2rad($pi);
        $oRad = deg2rad($o);

        // Meeus 23.2
        $dLon = (-$k * cos($oRad - $lonRad) + $e * $k * cos($piRad - $lonRad)) / cos($latRad);
        $dLat = -$k * sin($latRad) * (sin($oRad - $lonRad) - $e * sin($piRad - $lonRad));

        $lon += $dLon;
        $lat += $dLat;

        return new GeocentricEclipticalSphericalCoordinates($lon, $lat, $radiusVector);
    }
}
