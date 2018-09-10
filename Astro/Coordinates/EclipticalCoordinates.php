<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 17.01.15
 * Time: 09:15
 */

namespace App\Util\Astro\Coordinates;

use App\Util\Astro\AstronomicalObjects\Earth;
use App\Util\Astro\TimeOfInterest;

/**
 * Class EclipticalCoordinates
 * @package Coordinates
 */
class EclipticalCoordinates extends Coordinates
{
    private $longitude = 0.0;
    private $latitude = 0.0;
    private $elevation = 0.0;


    /**
     * Constructor
     * @param float $latitude
     * @param float $longitude
     * @param float $elevation
     * @param TimeOfInterest $toi
     */
    public function __construct($latitude, $longitude, $elevation, TimeOfInterest $toi)
    {
        parent::__construct($toi);

        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->elevation = $elevation;
    }


    /**
     * Get ecliptical latitude
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }


    /**
     * Get ecliptical longitude
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }


    /**
     * Get elevation
     * @return float
     */
    public function getElevation()
    {
        return $this->elevation;
    }


    /**
     * Get equatorial coordinates
     * @return EquatorialCoordinates
     */
    public function getEquatorialCoordinates()
    {
        $eps = $this->earth->getTrueObliquityOfEcliptic();
        $eps = deg2rad($eps);
        $lat = deg2rad($this->latitude);
        $lon = deg2rad($this->longitude);

        $ra = atan2(sin($lon) * cos($eps) - (sin($lat) / cos($lat)) * sin($eps), cos($lon));
        $ra = rad2deg($ra);
        $d = asin(sin($lat) * cos($eps) + cos($lat) * sin($eps) * sin($lon));
        $d = rad2deg($d);

        return new EquatorialCoordinates($ra, $d, $this->toi);
    }
}
