<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects;

use Andrmoel\AstronomyBundle\Calculations\EarthCalc;
use Andrmoel\AstronomyBundle\Calculations\SunCalc;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Earth;
use Andrmoel\AstronomyBundle\Calculations\TimeCalc;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalRectangularCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalSphericalCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\LocalHorizontalCoordinates;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

class Sun extends AstronomicalObject
{
    const TWILIGHT_DAY = 0;
    const TWILIGHT_CIVIL = 1;
    const TWILIGHT_NAUTICAL = 2;
    const TWILIGHT_ASTRONOMICAL = 3;
    const TWILIGHT_NIGHT = 4;

    public function getGeocentricEclipticalSphericalCoordinates(): GeocentricEclipticalSphericalCoordinates
    {
        $T = $this->T;

        $earth = new Earth($this->toi);
        $helEclSphCoordinates = $earth->getHeliocentricEclipticalSphericalCoordinates();

        // Meeus 25 higher accuracy
        $lon = $helEclSphCoordinates->getLongitude() + 180;
        $lat = $helEclSphCoordinates->getLatitude() * -1;

        $radiusVector = SunCalc::getDistanceToEarth($T);

        return new GeocentricEclipticalSphericalCoordinates($lon, $lat, $radiusVector);
    }

    public function getGeocentricEquatorialCoordinates(): GeocentricEquatorialCoordinates
    {
        // TODO Use method with higher accuracy (Meeus p.166)

        $T = $this->T;

        $L0 = SunCalc::getMeanLongitude($T);
        $M = SunCalc::getMeanAnomaly($T);

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
        $e = EarthCalc::getMeanObliquityOfEcliptic($T);
        $e = $e + 0.00256 * cos($ORad);
        $eRad = deg2rad($e);

        // Meeus 25.6
        $rightAscension = atan2(cos($eRad) * sin($lonRad), cos($lonRad));
        $rightAscension = AngleUtil::normalizeAngle(rad2deg($rightAscension));

        // Meeus 25.7
        $declination = asin(sin($eRad) * sin($oRad));
        $declination = rad2deg($declination);

        $radiusVector = SunCalc::getDistanceToEarth($T);

        return new GeocentricEquatorialCoordinates($rightAscension, $declination, $radiusVector);
    }

    public function getGeocentricEquatorialRectangularCoordinates(): GeocentricEclipticalRectangularCoordinates
    {
        $T = $this->T;

        $R = SunCalc::getRadiusVector($T);
        $eps = EarthCalc::getObliquityOfEcliptic($T);
        $epsRad = deg2rad($eps);
        $L0 = SunCalc::getMeanLongitude($T);
        $C = SunCalc::getEquationOfCenter($T);
        $M = SunCalc::getMeanAnomaly($T);
        $e = EarthCalc::getEccentricity($T);

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

    public function getEquationOfTime(): float
    {
        $T = $this->T;

        $L0 = SunCalc::getMeanLongitude($T);
        $geoEquCoordinates = $this->getGeocentricEquatorialCoordinates();
        $rightAscension = $geoEquCoordinates->getRightAscension();
        $dPhi = EarthCalc::getNutationInLongitude($T);
        $e = EarthCalc::getObliquityOfEcliptic($T);

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

    public function getSolarNoon(Location $location): TimeOfInterest
    {
        $lon = $location->getLongitude();
        $jd = $this->toi->getJulianDay(true) + $lon / 360;

        $Tnoon = TimeCalc::getJulianCenturiesFromJ2000($jd);
        $equationOfTime = $this->getEquationOfTime();

        $solNoonOffset = 720 - ($lon * 4) - $equationOfTime; // in minutes
        $Tnew = TimeCalc::getJulianCenturiesFromJ2000($jd + $solNoonOffset / 1440);

        var_dump($Tnoon, $Tnew);

        die();
        // TODO ...
    }
}
