<?php

namespace Andrmoel\AstronomyBundle\Corrections;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Earth;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Sun;
use Andrmoel\AstronomyBundle\Constants;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalSphericalCoordinates;
use Andrmoel\AstronomyBundle\TimeOfInterest;

class GeocentricEclipticalSphericalCorrections
{
    private $toi;

    private $earth;
    private $sun;

    public function __construct(TimeOfInterest $toi)
    {
        $this->toi = $toi;

        $this->earth = new Earth($this->toi);
        $this->sun = new Sun($this->toi);
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
        $lon = $geoEclSphCoordinates->getLongitude();
        $lat = $geoEclSphCoordinates->getLatitude();
        $radiusVector = $geoEclSphCoordinates->getRadiusVector();

        $dPhi = $this->earth->getNutationInLongitude();
        $dEps = $this->earth->getNutationInObliquity();

//        var_dump($dEps);die();

        $lon += $dPhi;
//        $lat -= $dEps; // TODO Korrekt???

        return new GeocentricEclipticalSphericalCoordinates($lon, $lat, $radiusVector);
    }

    public function correctEffectOfAberration(
        GeocentricEclipticalSphericalCoordinates $geoEclSphCoordinates
    ): GeocentricEclipticalSphericalCoordinates
    {
        $lon = $geoEclSphCoordinates->getLongitude();
        $lat = $geoEclSphCoordinates->getLatitude();
        $radiusVector = $geoEclSphCoordinates->getRadiusVector();

        $k = Constants::CONSTANT_OF_ABERRATION;
        $e = $this->earth->getEccentricity();
        $pi = $this->earth->getLongitudeOfPerihelionOfOrbit();
        $o = $this->sun->getTrueLongitude();


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
