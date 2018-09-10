<?php

namespace App\Util\Astro\AstronomicalObjects;

use App\Util\Astro\TimeOfInterest;
use App\Util\Astro\Util;

class Earth extends AstronomicalObject
{
    // Constants
    const RADIUS = 6378137.0; // Earth radius in km
    const FLATTENING = 0.00335281317789691440603238146967; // (1 / 298.257) Earth's flattening

    // Location of observer
    private $lat = 0;
    private $lon = 0;
    private $lonAstro = 0;
    private $elevation = 0;

    private $argumentsNutation = array(
        [0, 0, 0, 0, 1, -171996, -174.2, 92025, 8.9],
        [-2, 0, 0, 2, 2, -13187, -1.6, 5736, -3.1],
        [0, 0, 0, 2, 2, -2274, -0.2, 977, -0.5],
        [0, 0, 0, 0, 2, 2062, 0.2, -895, 0.5],
        [0, 1, 0, 0, 0, 1426, -3.4, 54, -0.1],
        [0, 0, 1, 0, 0, 712, 0.1, -7, 0],
        [-2, 1, 0, 2, 2, -517, 1.2, 224, -0.6],
        [0, 0, 0, 2, 1, -386, -0.4, 200, 0],
        [0, 0, 1, 2, 2, -301, 0, 129, -0.1],
        [-2, -1, 0, 2, 2, 217, -0.5, -95, 0.3],
        [-2, 0, 1, 0, 0, -158, 0, 0, 0],
        [-2, 0, 0, 2, 1, 129, 0.1, -70, 0],
        [0, 0, -1, 2, 2, 123, 0, -53, 0],
        [2, 0, 0, 0, 0, 63, 0, 0, 0],
        [0, 0, 1, 0, 1, 63, 0.1, -33, 0],
        [2, 0, -1, 2, 2, -59, 0, 26, 0],
        [0, 0, -1, 0, 1, -58, -0.1, 32, 0],
        [0, 0, 1, 2, 1, -51, 0, 27, 0],
        [-2, 0, 2, 0, 0, 48, 0, 0, 0],
        [0, 0, -2, 2, 1, 46, 0, -24, 0],
        [2, 0, 0, 2, 2, -38, 0, 16, 0],
        [0, 0, 2, 2, 2, -31, 0, 13, 0],
        [0, 0, 2, 0, 0, 29, 0, 0, 0],
        [-2, 0, 1, 2, 2, 29, 0, -12, 0],
        [0, 0, 0, 2, 0, 26, 0, 0, 0],
        [-2, 0, 0, 2, 0, -22, 0, 0, 0],
        [0, 0, -1, 2, 1, 21, 0, -10, 0],
        [0, 2, 0, 0, 0, 17, -0.1, 0, 0],
        [2, 0, -1, 0, 1, 16, 0, -8, 0],
        [-2, 2, 0, 2, 2, -16, 0.1, 7, 0],
        [0, 1, 0, 0, 1, -15, 0, 9, 0],
        [-2, 0, 1, 0, 1, -13, 0, 7, 0],
        [0, -1, 0, 0, 1, -12, 0, 6, 0],
        [0, 0, 2, -2, 0, 11, 0, 0, 0],
        [2, 0, -1, 2, 1, -10, 0, 5, 0],
        [2, 0, 1, 2, 2, -8, 0, 3, 0],
        [0, 1, 0, 2, 2, 7, 0, -3, 0],
        [-2, 1, 1, 0, 0, -7, 0, 0, 0],
        [0, -1, 0, 2, 2, -7, 0, 3, 0],
        [2, 0, 0, 2, 1, -7, 0, 3, 0],
        [2, 0, 1, 0, 0, 6, 0, 0, 0],
        [-2, 0, 2, 2, 2, 6, 0, -3, 0],
        [-2, 0, 1, 2, 1, 6, 0, -3, 0],
        [2, 0, -2, 0, 1, -6, 0, 3, 0],
        [2, 0, 0, 0, 1, -6, 0, 3, 0],
        [0, -1, 1, 0, 0, 5, 0, 0, 0],
        [-2, -1, 0, 2, 1, -5, 0, 3, 0],
        [-2, 0, 0, 0, 1, -5, 0, 3, 0],
        [0, 0, 2, 2, 1, -5, 0, 3, 0],
        [-2, 0, 2, 0, 1, 4, 0, 0, 0],
        [-2, 1, 0, 2, 1, 4, 0, 0, 0],
        [0, 0, 1, -2, 0, 4, 0, 0, 0],
        [-1, 0, 1, 0, 0, -4, 0, 0, 0],
        [-2, 1, 0, 0, 0, -4, 0, 0, 0],
        [1, 0, 0, 0, 0, -4, 0, 0, 0],
        [0, 0, 1, 2, 0, 3, 0, 0, 0],
        [0, 0, -2, 2, 2, -3, 0, 0, 0],
        [-1, -1, 1, 0, 0, -3, 0, 0, 0],
        [0, 1, 1, 0, 0, -3, 0, 0, 0],
        [0, -1, 1, 2, 2, -3, 0, 0, 0],
        [2, -1, -1, 2, 2, -3, 0, 0, 0],
        [0, 0, 3, 2, 2, -3, 0, 0, 0],
        [2, -1, 0, 2, 2, -3, 0, 0, 0],
    );

    // Sum parameter
    private $sumPhi = 0;
    private $sumEps = 0;


    public function __construct()
    {
        parent::__construct();
        $this->initializeSumParameter();
    }


    /**
     * Set time of interest
     * @param TimeOfInterest $toi
     */
    public function setTimeOfInterest(TimeOfInterest $toi)
    {
        parent::setTimeOfInterest($toi);
        $this->initializeSumParameter();
    }


    /**
     * Initialize sum parameter
     */
    private function initializeSumParameter()
    {
        $T = $this->T;

        // Mean elongation of the moon from the sun
        $D = 297.85036 + 445267.111480 * $T - 0.0019142 * pow($T, 2) + pow($T, 3) / 189474;
        $D = Util::normalizeAngle($D);
        // Mean anomaly of the sun (earth)
        $Msun = 357.52772 + 35999.050340 * $T - 0.0001603 * pow($T, 2) - pow($T, 3) / 300000;
        $Msun = Util::normalizeAngle($Msun);
        // Mean anomaly of the moon
        $Mmoon = 134.96298 + 477198.867398 * $T + 0.0086972 * pow($T, 2) + pow($T, 3) / 56250;
        $Mmoon = Util::normalizeAngle($Mmoon);
        // Moon's argument of latitude
        $F = 93.27191 + 483202.017538 * $T - 0.0036825 * pow($T, 2) + pow($T, 3) / 327270;
        $F = Util::normalizeAngle($F);
        // Longitude of the ascending node of moon's mean orbit on ecliptic
        $O = 125.04452 - 1934.136261 * $T + 0.0020708 * pow($T, 2) + pow($T, 3) / 450000;
        $O = Util::normalizeAngle($O);

        $sumPhi = 0;
        $sumEps = 0;
        foreach ($this->argumentsNutation as $args) {
            $argD = $args[0];
            $argMsun = $args[1];
            $argMmoon = $args[2];
            $argF = $args[3];
            $argO = $args[4];
            $argPhi1 = $args[5];
            $argPhi2 = $args[6];
            $argEps1 = $args[7];
            $argEps2 = $args[8];

            $tmpSum = $argD * $D + $argMsun * $Msun + $argMmoon * $Mmoon + $argF * $F + $argO * $O;

            $sumPhi += sin(deg2rad($tmpSum)) * ($argPhi1 + $argPhi2 * $T);
            $sumEps += cos(deg2rad($tmpSum)) * ($argEps1 + $argEps2 * $T);
        }

        $sumPhi *= 0.0001 / 3600;
        $sumEps *= 0.0001 / 3600;

        $this->sumPhi = $sumPhi;
        $this->sumEps = $sumEps;
    }


    /**
     * Set location
     * @param float $lat
     * @param float $lon
     */
    public function setLocation($lat, $lon)
    {
        $this->lat = $lat;
        $this->lon = $lon;
        $this->lonAstro = -$lon;
    }


    /**
     * Set observer's elevation above sea level
     * @param float $elevation
     */
    public function setElevation($elevation)
    {
        $this->elevation = $elevation;
    }


    /**
     * Get latitude of observer
     * @return float
     */
    public function getLatitude()
    {
        return $this->lat;
    }


    /**
     * Get longitude of observer
     * @return float
     */
    public function getLongitude()
    {
        return $this->lon;
    }


    /**
     * Get negative longitude of observer
     * @return float
     */
    public function getLongitudeAstro()
    {
        return $this->lonAstro;
    }


    /**
     * Get elevation of observer
     * @return int
     */
    public function getElevation()
    {
        return $this->elevation;
    }


    /**
     * Get earth radius at equator
     * @return float
     */
    public function getRadius()
    {
        return self::RADIUS / 100;
    }


    /**
     * Get earth's flattening
     * @return float
     */
    public function getFlattening()
    {
        return self::FLATTENING;
    }


    /**
     * Get eccentricity of earth's meridian
     * @return float
     */
    public function getEccentricity()
    {
        $e = sqrt(2 * self::FLATTENING - pow(self::FLATTENING, 2));

        return $e;
    }


    /**
     * Get nutation
     * return float
     */
    public function getNutation()
    {
        return $this->sumPhi;
    }


    /**
     * Get obliquity of ecliptic
     * @return float
     */
    public function getObliquityOfEcliptic()
    {
        $T = $this->T;
        $U = $T / 100;

        $e0 = Util::angle2dec(23, 26, 21.448)
            - Util::angle2dec(0, 0, 4680.93) * $U
            - 1.55 * pow($U, 2)
            + 1999.25 * pow($U, 3)
            - 51.38 * pow($U, 4)
            - 249.67 * pow($U, 5)
            - 39.05 * pow($U, 6)
            + 7.12 * pow($U, 7)
            + 27.87 * pow($U, 8)
            + 5.79 * pow($U, 9)
            + 2.45 * pow($U, 10);

        return $e0;
    }


    /**
     * Get apparent (true) obliquity of ecliptic
     * return float
     */
    public function getTrueObliquityOfEcliptic()
    {
        $e0 = $this->getObliquityOfEcliptic();
        $e = $e0 + $this->sumEps;

        return $e;
    }


    /**
     * Get distance between 2 points on earths surface in km
     * @param $lat1
     * @param $lon1
     * @param $lat2
     * @param $lon2
     * @return float
     */
    public function getDistance($lat1, $lon1, $lat2, $lon2)
    {
        $F = deg2rad(($lat1 + $lat2) / 2);
        $G = deg2rad(($lat1 - $lat2) / 2);
        $l = deg2rad(($lon1 - $lon2) / 2);

        $S = pow(sin($G), 2) * pow(cos($l), 2) + pow(cos($F), 2) * pow(sin($l), 2);
        $C = pow(cos($G), 2) * pow(cos($l), 2) + pow(sin($F), 2) * pow(sin($l), 2);

        $o = atan(sqrt($S / $C));
        $R = sqrt($S * $C) / $o;

        $D = 2 * $o * (self::RADIUS / 100);
        $H1 = (3 * $R - 1) / (2 * $C);
        $H2 = (3 * $R + 1) / (2 * $S);

        $s = $D * (1 + self::FLATTENING * $H1 * pow(sin($F), 2) * pow(cos($G), 2)
            - self::FLATTENING * $H2 * pow(cos($F), 2) * pow(sin($G), 2));

        return $s;
    }


    /**
     * TODO wird zur SoFi Berechnung benötigt ...
     * @return array
     */
    public function getXY()
    {
        $lat = deg2rad($this->lat);
        $lon = deg2rad($this->lat);
        $r = (self::RADIUS / 100) * 1000;

        $u = atan(0.99664719 * tan($lat));
        $x = cos($u) + $this->elevation / $r * cos($lat);
        $y = 0.99664719 * sin($u) + $this->elevation / $r * sin($lat);

        return array(
            'x' => $x,
            'y' => $y,
        );
    }
}
