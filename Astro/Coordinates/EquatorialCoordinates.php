<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 17.01.15
 * Time: 09:11
 */

namespace App\Util\Astro\Coordinates;

use App\Util\Astro\AstronomicalObjects\Earth;
use App\Util\Astro\TimeOfInterest;
use App\Util\Astro\Util;

/**
 * Class EquatorialCoordinates
 * @package Coordinates
 */
class EquatorialCoordinates extends Coordinates
{
    private $rightAscension = 0;
    private $declination = 0;


    /**
     * Constructor
     * @param float $rightAscension
     * @param float $declination
     * @param TimeOfInterest $toi
     */
    public function __construct($rightAscension, $declination, TimeOfInterest $toi)
    {
        parent::__construct($toi);

        $this->rightAscension = $rightAscension;
        $this->declination = $declination;
    }


    /**
     * Get right ascension
     * @return float
     */
    public function getRightAscension()
    {
        return $this->rightAscension;
    }


    /**
     * Get declination
     * @return float
     */
    public function getDeclination()
    {
        return $this->declination;
    }


    /**
     * Get ecliptical coordinates
     * @return EclipticalCoordinates
     */
    public function getEclipticalCoordinates()
    {
        $a = deg2rad($this->rightAscension);
        $d = deg2rad($this->declination);
        $e = deg2rad($this->earth->getObliquityOfEcliptic());

        $lon = atan((sin($a) * cos($e) + tan($d) * sin($e)) / cos($a));
        $lon = rad2deg($lon);
        $lat = asin(sin($d) * cos($e) - cos($d) * sin($e) * sin($a));
        $lat = rad2deg($lat);

        return new EclipticalCoordinates($lat, $lon, 0, $this->toi);
    }


    /**
     * Get horizontal coordinates
     * @param Earth $earth
     * @return HorizontalCoordinates
     */
    public function getHorizontalCoordinates(Earth $earth)
    {
        $obsLat = $earth->getLatitude();
        $obsLatRad = deg2rad($obsLat);
        $obsLon = $earth->getLongitudeAstro();
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
