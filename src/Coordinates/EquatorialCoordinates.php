<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Util;

class EquatorialCoordinates extends Coordinates
{
    private $rightAscension = 0;
    private $declination = 0;

    public function __construct(float $rightAscension, float $declination)
    {
        parent::__construct();

        $this->rightAscension = $rightAscension;
        $this->declination = $declination;
    }

    public function getRightAscension(): float
    {
        return $this->rightAscension;
    }

    public function getDeclination(): float
    {
        return $this->declination;
    }

    public function getEclipticalCoordinates(float $obliquityOfEcliptic): EclipticalCoordinates
    {
        $a = deg2rad($this->rightAscension);
        $d = deg2rad($this->declination);
        $eps = deg2rad($obliquityOfEcliptic);

        $lon = atan((sin($a) * cos($eps) + tan($d) * sin($eps)) / cos($a));
        $lon = rad2deg($lon) + 180; // TODO warum + 180? Laut buch nicht nötig...
        $lat = asin(sin($d) * cos($eps) - cos($d) * sin($eps) * sin($a));
        $lat = rad2deg($lat);

        return new EclipticalCoordinates($lat, $lon);
    }

    public function getLocalHorizontalCoordinates(Location $location, TimeOfInterest $toi): LocalHorizontalCoordinates
    {
        $latRad = $location->getLatitudeRad();
        $lon = $location->getLongitudePositiveWest();
        $agmst = $toi->getApparentGreenwichMeanSiderealTime();
        $d = deg2rad($this->declination);

        // Calculate hour angle
        $H = $agmst - $lon - $this->rightAscension;
        $H = Util::normalizeAngle($H);
        $H = deg2rad($H);

        // Meeus 13.5
        $azimuth = atan(sin($H) / (cos($H) * sin($latRad) - tan($d) * cos($latRad)));
        $azimuth = rad2deg($azimuth) + 180; // Add 180° to get azimuth from north (else it is from south)
        $azimuth = Util::normalizeAngle($azimuth);

        // Meeus 13.6
        $altitude = asin(sin($latRad) * sin($d) + cos($latRad) * cos($d) * cos($H));
        $altitude = rad2deg($altitude);

        return new LocalHorizontalCoordinates($azimuth, $altitude);
    }
}
