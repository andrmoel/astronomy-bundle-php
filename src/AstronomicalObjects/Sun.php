<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects;

use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalRectangularCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalSphericalCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\LocalHorizontalCoordinates;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

class Sun extends AstronomicalObject
{
    const TWILIGHT_DAY = 0;
    const TWILIGHT_CIVIL = 1;
    const TWILIGHT_NAUTICAL = 2;
    const TWILIGHT_ASTRONOMICAL = 3;
    const TWILIGHT_NIGHT = 4;

    public function getMeanLongitude(): float
    {
        $t = $this->toi->getJulianMillenniaFromJ2000();

        // TODO Where did I found the formular?
//        $L0 = 280.46646
//            + 36000.76983 * $T
//            + 0.0003032 * pow($T, 2);

        // Meeus 28.2
        $L0 = 280.4664567
            + 360007.6982779 * $t
            + 0.03042028 * pow($t, 2)
            + pow($t, 3) / 49931
            - pow($t, 4) / 15300
            + pow($t, 5) / 2000000;
        $L0 = AngleUtil::normalizeAngle($L0);

        return $L0;
    }

    /**
     * Same as earth's
     * @return float
     */
    public function getMeanAnomaly(): float
    {
        $T = $this->T;

        // Meeus chapter 22
//        $M = 357.52772
//            + 35999.050340 * $T
//            - 0.0001603 * pow($T, 2)
//            - pow($T, 3) / 300000;

        // Meeus 47.4
        $M = 357.5291092
            + 35999.0502909 * $T
            - 0.0001536 * pow($T, 2)
            + pow($T, 3) / 2449000;
        $M = AngleUtil::normalizeAngle($M);

        return $M;
    }

    public function getRadiusVector(): float
    {
        $earth = new Earth($this->toi);

        $T = $this->T;

        $e = $earth->getEccentricity();
        $M = $this->getMeanAnomaly();

        // Meeus 25.4
        $C = (1.914602 - 0.004817 * $T - 0.000014 * pow($T, 2)) * sin(deg2rad($M));
        $C += (0.019993 - 0.000101 * $T) * sin(2 * deg2rad($M));
        $C += 0.000289 * sin(3 * deg2rad($M));

        // True anomaly
        $v = $M + $C;
        $vRad = deg2rad($v);

        // Meeus 25.5
        $R = (1000001018 * (1 - pow($e, 2))) / (1 + $e * cos($vRad));
        $R /= 1000000000; // TODO Warum durch das? ...

        return $R;
    }

    public function getGeocentricEclipticalSphericalCoordinates(): GeocentricEclipticalSphericalCoordinates
    {
        $earth = new Earth($this->toi);
        $obliquityOfEcliptic = $earth->getObliquityOfEcliptic();

        return $this
            ->getGeocentricEquatorialCoordinates()
            ->getGeocentricEclipticalSphericalCoordinates($obliquityOfEcliptic);
    }

    public function getGeocentricEquatorialCoordinates(): GeocentricEquatorialCoordinates
    {
        // TODO Use method with higher accuracy (Meeus p.166)
        $earth = new Earth($this->toi);

        $T = $this->T;

        $L0 = $this->getMeanLongitude();
        $M = $this->getMeanAnomaly();

        $C = (1.914602 - 0.004817 * $T - 0.000014 * pow($T, 2)) * sin(deg2rad($M))
            + (0.019993 - 0.000101 * $T) * sin(2 * deg2rad($M))
            + 0.000289 * sin(3 * deg2rad($M));

        // True longitude (o) and true anomaly (v)
        $o = $L0 + $C;
        $oRad = deg2rad($o);

        $O = 125.04 - 1934.136 * $T;
        $ORad = deg2rad($O);
        $lon = $o - 0.00569 - 0.00478 * sin($ORad);
        $lonRad = deg2rad($lon);

        // Meeus 25.8 - Corrections
        $e = $earth->getMeanObliquityOfEcliptic();
        $e = $e + 0.00256 * cos($ORad);
        $eRad = deg2rad($e);

        // Meeus 25.6
        $rightAscension = atan2(cos($eRad) * sin($lonRad), cos($lonRad));
        $rightAscension = AngleUtil::normalizeAngle(rad2deg($rightAscension));

        // Meeus 25.7
        $declination = asin(sin($eRad) * sin($oRad));
        $declination = rad2deg($declination);

        return new GeocentricEquatorialCoordinates($rightAscension, $declination);
    }

    public function getGeocentricEquatorialRectangularCoordinates(): GeocentricEclipticalRectangularCoordinates
    {
        $R = $this->getRadiusVector();
        $earth = new Earth($this->toi);

        $T = $this->T;

        $eps = $earth->getObliquityOfEcliptic();
        $epsRad = deg2rad($eps);
        $L0 = $this->getMeanLongitude();
        $M = $this->getMeanAnomaly();
        $e = $earth->getEccentricity();

        $C = (1.914602 - 0.004817 * $T - 0.000014 * pow($T, 2)) * sin(deg2rad($M));
        $C += (0.019993 - 0.000101 * $T) * sin(2 * deg2rad($M));
        $C += 0.000289 * sin(3 * deg2rad($M));

        // True longitude
        $o = $L0 + $C;
        $oRad = deg2rad($o);

        // TODO How do is calculate it?
        $bRad = AngleUtil::angle2dec('0°0\'0.62"');

        $X = $R * cos($bRad) * cos($oRad);
        $Y = $R * (cos($bRad) * sin($oRad) * cos($epsRad) - sin($bRad) * sin($epsRad));
        $Z = $R * (cos($bRad) * sin($oRad) * sin($epsRad) + sin($bRad) * cos($epsRad));

        return new GeocentricEclipticalRectangularCoordinates($X, $Y, $Z);
    }

    public function getLocalHorizontalCoordinates(Location $location): LocalHorizontalCoordinates
    {
        return $this
            ->getGeocentricEquatorialCoordinates()
            ->getLocalHorizontalCoordinates($location, $this->toi);
    }

    /**
     * Get distance to earth [km]
     * @return float
     */
    public function getDistanceToEarth(): float
    {
        $R = $this->getRadiusVector();

        // https://www.iau.org/static/resolutions/IAU2012_English.pdf
        $r = $R * 149597870.7;

        return $r;
    }

    public function getEquationOfTime(): float
    {
        $earth = new Earth($this->toi);

        $L0 = $this->getMeanLongitude();
        $geoEquCoordinates = $this->getGeocentricEquatorialCoordinates();
        $rightAscension = $geoEquCoordinates->getRightAscension();
        $dPhi = $earth->getNutation();
        $e = $earth->getObliquityOfEcliptic();

        // Meeus 28.1
        $E = $L0 - 0.0057183 - $rightAscension + $dPhi * cos($e);

        return $E;
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
