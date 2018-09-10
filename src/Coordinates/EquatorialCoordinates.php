<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Earth;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Util;

class EquatorialCoordinates extends Coordinates
{
    private $rightAscension = 0;
    private $declination = 0;


    public function __construct(float $rightAscension, float $declination, TimeOfInterest $toi)
    {
        parent::__construct($toi);

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


    public function getEclipticalCoordinates(): EclipticalCoordinates
    {
        $a = deg2rad($this->rightAscension);
        $d = deg2rad($this->declination);
        $e = deg2rad($this->earth->getObliquityOfEcliptic());

        $lon = atan((sin($a) * cos($e) + tan($d) * sin($e)) / cos($a));
        $lon = rad2deg($lon);
        $lat = asin(sin($d) * cos($e) - cos($d) * sin($e) * sin($a));
        $lat = rad2deg($lat);

        return new EclipticalCoordinates($lat, $lon, $this->toi);
    }


    public function getHorizontalCoordinates(Earth $earth): HorizontalCoordinates
    {
        $obsLat = $earth->getLocation()->getLatitude();
        $obsLatRad = deg2rad($obsLat);
        $obsLon = -1 * $earth->getLocation()->getLongitude();
        $gmst = $this->toi->getApparentGreenwichMeanSiderealTime();
        $d = deg2rad($this->declination);

        // Calculate hour angle
        $H = Util::normalizeAngle($gmst - $obsLon - $this->rightAscension);
        $H = deg2rad($H);

        // Calculate azimuth and altitude
        $A = atan(sin($H) / (cos($H) * sin($obsLatRad) - tan($d) * cos($obsLatRad)));
        $A = rad2deg($A);
        $h = asin(sin($obsLatRad) * sin($d) + cos($obsLatRad) * cos($d) * cos($H));
        $h = rad2deg($h);

        return new HorizontalCoordinates($A, $h, $this->toi);
    }
}
