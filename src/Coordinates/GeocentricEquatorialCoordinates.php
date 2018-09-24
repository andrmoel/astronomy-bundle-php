<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

class GeocentricEquatorialCoordinates
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

    public function getGeocentricEclipticalSphericalCoordinates(
        float $obliquityOfEcliptic
    ): GeocentricEclipticalSphericalCoordinates
    {
        $a = deg2rad($this->rightAscension);
        $d = deg2rad($this->declination);
        $eps = deg2rad($obliquityOfEcliptic);

        // Meeus 13.1
        $lon = atan((sin($a) * cos($eps) + tan($d) * sin($eps)) / cos($a));
        $lon = rad2deg($lon) + 180; // TODO warum + 180? Laut buch nicht nötig...

        // Meeus 13.2
        $lat = asin(sin($d) * cos($eps) - cos($d) * sin($eps) * sin($a));
        $lat = rad2deg($lat);

        return new GeocentricEclipticalSphericalCoordinates($lon, $lat, $this->radiusVector);
    }

    public function getLocalHorizontalCoordinates(Location $location, TimeOfInterest $toi): LocalHorizontalCoordinates
    {
        $latRad = $location->getLatitudeRad();
        $lon = $location->getLongitudePositiveWest();
        $agmst = $toi->getApparentGreenwichMeanSiderealTime(); // TODO Apparent oder doch lieber mean?
        $d = deg2rad($this->declination);

        // Calculate hour angle
        $H = $agmst - $lon - $this->rightAscension;
        $H = AngleUtil::normalizeAngle($H);
        $H = deg2rad($H);

        // Meeus 13.5
        $azimuth = atan(sin($H) / (cos($H) * sin($latRad) - tan($d) * cos($latRad)));
        $azimuth = rad2deg($azimuth) + 180; // Add 180° to get azimuth from north (else it is from south)
        $azimuth = AngleUtil::normalizeAngle($azimuth);

        // Meeus 13.6
        $altitude = asin(sin($latRad) * sin($d) + cos($latRad) * cos($d) * cos($H));
        $altitude = rad2deg($altitude);

        return new LocalHorizontalCoordinates($azimuth, $altitude);
    }
}
