<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects;

use Andrmoel\AstronomyBundle\Coordinates\EclipticalCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\EquatorialCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\LocalHorizontalCoordinates;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Util;

class Sun extends AstronomicalObject
{
    const TWILIGHT_DAY = 0;
    const TWILIGHT_CIVIL = 1;
    const TWILIGHT_NAUTICAL = 2;
    const TWILIGHT_ASTRONOMICAL = 3;
    const TWILIGHT_NIGHT = 4;


    public function getEclipticalCoordinates(): EclipticalCoordinates
    {
        $earth = new Earth($this->toi);
        $obliquityOfEcliptic = $earth->getObliquityOfEcliptic();

        return $this
            ->getEquatorialCoordinates()
            ->getEclipticalCoordinates($obliquityOfEcliptic);
    }


    public function getEquatorialCoordinates(): EquatorialCoordinates
    {
        $T = $this->T;

        // Get obliquity of ecliptic
        $earth = new Earth($this->toi);
        $eps = $earth->getTrueObliquityOfEcliptic();
        $eps = deg2rad($eps);

        // Geometric mean longitude of the sun
        $L = 280.46646 + 36000.76983 * $T + 0.0003032 * pow($T, 2);

        // Mean anomaly of the sun
        $M = 357.52911 + 35999.05029 * $T - 0.0001537 * pow($T, 2);

        // Eccentricity of earth's orbit
        // TODO needed?
//        $e = 0.016708634 - 0.000042037 * $T - 0.0000001267 * pow($T, 2);

        $C = (1.914602 - 0.004817 * $T - 0.000014 * pow($T, 2)) * sin(deg2rad($M));
        $C += (0.019993 - 0.000101 * $T) * sin(2 * deg2rad($M));
        $C += 0.000289 * sin(3 * deg2rad($M));

        $o = $L + $C;
        $oRad = deg2rad($o);

        $O = 125.04 - 1934.136 * $T;
        $ORad = deg2rad($O);
        $lon = $o - 0.00569 - 0.00478 * sin($ORad);
        $lonRad = deg2rad($lon);

        // Corrections
        $eps += 0.00256 * cos($ORad);

        $a = atan2(cos($eps) * sin($lonRad), cos($lonRad));
        $a = Util::normalizeAngle(rad2deg($a));

        $d = asin(sin($eps) * sin($oRad));
        $d = rad2deg($d);

        return new EquatorialCoordinates($a, $d);
    }


    /**
     * TODO ...
     * @return GeocentricCoordinates
     */
    public function getGeocentricCoordinates(): GeocentricCoordinates
    {
        // TODO ...

        // Get obliquity of ecliptic
        $earth = new Earth($this->toi);
        $eps = $earth->getTrueObliquityOfEcliptic();
        $eps = deg2rad($eps);

        $R = 0.99760775;

//        $x = $R * cos($b) * cos($t);

        $x = 0;
        $y = 0;
        $z = 0;

        return new GeocentricCoordinates($x, $y, $z, $this->toi);
    }


    public function getLocalHorizontalCoordinates(Location $location): LocalHorizontalCoordinates
    {
        return $this
            ->getEquatorialCoordinates()
            ->getLocalHorizontalCoordinates($location, $this->toi);
    }


    /**
     * Get distance to earth [km]
     * @return float
     */
    public function getDistanceToEarth()
    {
        return 149971520; // TODO
    }


    public function getTwilight(Location $location): int
    {
        $localHorizontalCoordinates = $this->getLocalHorizontalCoordinates($location);
        $alt = $localHorizontalCoordinates->getAltitude();

        if ($alt > 0) {
            return self::TWILIGHT_DAY;
        }

        if ($alt > -6) {
            return self::TWILIGHT_CIVIL;
        }

        if ($alt > -12) {
            return self::TWILIGHT_NAUTICAL;
        }

        if ($alt > -18) {
            return self::TWILIGHT_ASTRONOMICAL;
        }

        return self::TWILIGHT_NIGHT;
    }
}
