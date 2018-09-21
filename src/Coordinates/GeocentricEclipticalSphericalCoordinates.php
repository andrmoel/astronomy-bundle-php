<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Earth;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Sun;
use Andrmoel\AstronomyBundle\Constants;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

class GeocentricEclipticalSphericalCoordinates extends EclipticalSphericalCoordinates
{
    // TODO Test ...
    public function getGeocentricEclipticalRectangularCoordinates(): GeocentricEclipticalRectangularCoordinates
    {
        $lonRad = deg2rad($this->longitude);
        $latRad = deg2rad($this->latitude);

        $X = $this->radiusVector * cos($latRad) * cos($lonRad);
        $Y = $this->radiusVector * cos($latRad) * sin($lonRad);
        $Z = $this->radiusVector * sin($latRad);

        return new GeocentricEclipticalRectangularCoordinates($X, $Y, $Z);
    }

    public function getGeocentricEquatorialCoordinates(float $obliquityOfEcliptic): GeocentricEquatorialCoordinates
    {
        $eps = deg2rad($obliquityOfEcliptic);
        $lon = deg2rad($this->longitude);
        $lat = deg2rad($this->latitude);

        // Meeus 13.3
        $rightAscension = atan2(sin($lon) * cos($eps) - (sin($lat) / cos($lat)) * sin($eps), cos($lon));
        $rightAscension = rad2deg($rightAscension);

        // Meeus 13.4
        $declination = asin(sin($lat) * cos($eps) + cos($lat) * sin($eps) * sin($lon));
        $declination = rad2deg($declination);

        return new GeocentricEquatorialCoordinates($rightAscension, $declination, $this->radiusVector);
    }


    // TODO Name und so...
    // TODO Test schreiben
    public function apparent(TimeOfInterest $toi)
    {
        $lonRad = deg2rad($this->longitude);
        $latRad = deg2rad($this->latitude);

        $earth = new Earth($toi);
        $sun = new Sun($toi);

        $k = Constants::CONSTANT_OF_ABERRATION;
        $e = $earth->getEccentricity();
        $pi = $earth->getLongitudeOfPerihelionOfOrbit();
        $piRad = deg2rad($pi);
        $o = $sun->getTrueLongitude();
        $oRad = deg2rad($o);

        // Meeus 23.2
        $dLon = (-$k * cos($oRad - $lonRad) + $e * $k * cos($piRad - $lonRad)) / cos($latRad);
        $dLat = -$k * sin($latRad) * (sin($oRad - $lonRad) - $e * sin($piRad - $lonRad));

        var_dump($dLon, $dLat);

        var_dump($this->longitude + $dLon, $this->latitude + $dLat);die();

        die();
    }
}
