<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Earth;
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
        $H = deg2rad($H);

        // Calculate azimuth and altitude
        $A = atan(sin($H) / (cos($H) * sin($latRad) - tan($d) * cos($latRad)));
        $A = rad2deg($A);
        $h = asin(sin($latRad) * sin($d) + cos($latRad) * cos($d) * cos($H));
        $h = rad2deg($h);

        return new LocalHorizontalCoordinates($A, $h);
    }
}
