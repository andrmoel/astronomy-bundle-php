<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Earth;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

class GeocentricEclipticalSphericalCoordinates extends EclipticalSphericalCoordinates
{
    // TODO Test
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
        $earth = new Earth($toi);
        $eps = $earth->getMeanObliquityOfEcliptic();

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
