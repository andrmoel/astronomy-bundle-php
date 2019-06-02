<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Calculations\CoordinateTransformations;
use Andrmoel\AstronomyBundle\Calculations\TimeCalc;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

class GeocentricEquatorialSphericalCoordinates extends AbstractEquatorialSphericalCoordinates
{
    public function getGeocentricEclipticalRectangularCoordinates(float $T): GeocentricEclipticalRectangularCoordinates
    {
        return $this
            ->getGeocentricEclipticalSphericalCoordinates($T)
            ->getGeocentricEclipticalRectangularCoordinates();
    }

    public function getGeocentricEclipticalSphericalCoordinates(float $T): GeocentricEclipticalSphericalCoordinates
    {
        $coord = CoordinateTransformations::equatorialSpherical2eclipticalSpherical(
            $this->rightAscension,
            $this->declination,
            $this->radiusVector,
            $T
        );

        return new GeocentricEclipticalSphericalCoordinates($coord[0], $coord[1], $coord[2]);
    }

    public function getGeocentricEquatorialRectangularCoordinates(): GeocentricEquatorialRectangularCoordinates
    {
        $coord = CoordinateTransformations::spherical2rectangular(
            $this->rightAscension,
            $this->declination,
            $this->radiusVector
        );

        return new GeocentricEquatorialRectangularCoordinates($coord[0], $coord[1], $coord[2]);
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
        $azimuthRad = atan2(sin($HRad), (cos($HRad) * sin($latRad) - tan($dRad) * cos($latRad)));
        $azimuth = AngleUtil::normalizeAngle(rad2deg($azimuthRad) + 180);

        // Meeus 13.6
        $altitudeRad = asin(sin($latRad) * sin($dRad) + cos($latRad) * cos($dRad) * cos($HRad));
        $altitude = rad2deg($altitudeRad);

        return new LocalHorizontalCoordinates($azimuth, $altitude);
    }
}
