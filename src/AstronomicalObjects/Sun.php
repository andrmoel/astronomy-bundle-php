<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Earth;
use Andrmoel\AstronomyBundle\Calculations\EarthCalc;
use Andrmoel\AstronomyBundle\Calculations\RiseAndSetCalc;
use Andrmoel\AstronomyBundle\Calculations\SunCalc;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalRectangularCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalSphericalCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\LocalHorizontalCoordinates;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

class Sun extends AstronomicalObject implements AstronomicalObjectInterface
{
    const TWILIGHT_DAY = 0;
    const TWILIGHT_CIVIL = 1;
    const TWILIGHT_NAUTICAL = 2;
    const TWILIGHT_ASTRONOMICAL = 3;
    const TWILIGHT_NIGHT = 4;

//    public function getGeocentricEclipticalSphericalCoordinates(): GeocentricEclipticalSphericalCoordinates
//    {
//        $T = $this->T;
//
//        $lat = SunCalc::getApparentLongitude($T);
//        $lon = SunCalc::getApparentLongitude($T);
//
//        return new GeocentricEclipticalSphericalCoordinates($lon, $lat, $radiusVector);
//    }

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
        $T = $this->T;

        $rightAscension = SunCalc::getApparentRightAscension($T);
        $declination = SunCalc::getApparentDeclination($T);
        $radiusVector = SunCalc::getRadiusVector($T);

        return new GeocentricEquatorialCoordinates($rightAscension, $declination, $radiusVector);
    }

    /**
     * @return GeocentricEclipticalRectangularCoordinates
     * @throws \Exception
     * @deprecated Not yet working, perfectly
     */
    public function getGeocentricEquatorialRectangularCoordinates(): GeocentricEclipticalRectangularCoordinates
    {
        $T = $this->T;

        $R = SunCalc::getRadiusVector($T);
        $eps = EarthCalc::getTrueObliquityOfEcliptic($T);
        $epsRad = deg2rad($eps);
        $L0 = SunCalc::getMeanLongitude($T);
        $C = SunCalc::getEquationOfCenter($T);
        $M = SunCalc::getMeanAnomaly($T);
        $e = EarthCalc::getEccentricity($T);

        // True longitude
        $o = $L0 + $C;
        $oRad = deg2rad($o);

        // TODO How do we calculate this one?
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

    public function getSunrise(Location $location): TimeOfInterest
    {
        $ras = new RiseAndSetCalc(Sun::class, $location, $this->toi);
        return $ras->getRise();
    }

    public function getUpperCulmination(Location $location): TimeOfInterest
    {
        $ras = new RiseAndSetCalc(Sun::class, $location, $this->toi);
        return $ras->getTransit();
    }

    public function getSunset(Location $location): TimeOfInterest
    {
        $ras = new RiseAndSetCalc(Sun::class, $location, $this->toi);
        return $ras->getSet();
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
