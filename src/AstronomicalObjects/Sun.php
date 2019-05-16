<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Earth;
use Andrmoel\AstronomyBundle\Calculations\EarthCalc;
use Andrmoel\AstronomyBundle\Calculations\TimeCalc;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Earth;
use Andrmoel\AstronomyBundle\Calculations\EarthCalc;
use Andrmoel\AstronomyBundle\Calculations\RiseAndSetCalc;
use Andrmoel\AstronomyBundle\Calculations\SunCalc;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalRectangularCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalSphericalCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialRectangularCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialSphericalCoordinates;
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
        $helEclSphCoord = $earth->getHeliocentricEclipticalSphericalCoordinates();

        $B = $helEclSphCoord->getLatitude();
        $L = $helEclSphCoord->getLongitude();
        $R = $helEclSphCoord->getRadiusVector();

        $beta = -1 * $B;
        $Theta = $L + 180;

        // Convert to FK5lon
        $lonC = $Theta - 1.397 * $T - 0.00031 * pow($T, 2);
        $lonCRad = deg2rad($lonC);

        // Meeus 25.9
        $dTheta = -1 * AngleUtil::angle2dec('0°0\'0.09033"');
        $dBeta = AngleUtil::angle2dec('0°0\'0.03916"') * (cos($lonCRad) - sin($lonCRad));

        $lat = $beta + $dBeta;
        $lon = $Theta + $dTheta;

        // Corrections
        $dPhi = EarthCalc::getNutationInLongitude($T);

        // Meeus 25.10
        $lon = $lon + $dPhi - 0.005691611111 / $R;
        $lon = AngleUtil::normalizeAngle($lon);

        return new GeocentricEclipticalSphericalCoordinates($lat, $lon, $R);
    }

    public function getGeocentricEquatorialRectangularCoordinates(): GeocentricEquatorialRectangularCoordinates
    {
        $T = $this->T;

        $earth = new Earth($this->toi);
        $helEclSphCoord = $earth->getHeliocentricEclipticalSphericalCoordinates();

        $B = $helEclSphCoord->getLatitude();
        $L = $helEclSphCoord->getLongitude();
        $R = $helEclSphCoord->getRadiusVector();

        $beta = -1 * $B;
        $Theta = $L + 180;
        $Theta = AngleUtil::normalizeAngle($Theta);
        $eps0 = EarthCalc::getMeanObliquityOfEcliptic($T);

        $epsRad = deg2rad($eps0);
        $latRad = deg2rad($beta);
        $lonRad = deg2rad($Theta);

        $X = $R * cos($latRad) * cos($lonRad);
        $Y = $R * (cos($latRad) * sin($lonRad) * cos($epsRad) - sin($latRad) * sin($epsRad));
        $Z = $R * (cos($latRad) * sin($lonRad) * sin($epsRad) + sin($latRad) * cos($epsRad));

        return new GeocentricEquatorialRectangularCoordinates($X, $Y, $Z);
    }

    public function getGeocentricEquatorialSphericalCoordinates(): GeocentricEquatorialSphericalCoordinates
    {
        return $this
            ->getGeocentricEclipticalSphericalCoordinates()
            ->getGeocentricEquatorialSphericalCoordinates($this->T);
    }

    public function getLocalHorizontalCoordinates(Location $location): LocalHorizontalCoordinates
    {
        return $this
            ->getGeocentricEquatorialSphericalCoordinates()
            ->getLocalHorizontalCoordinates($location, $this->T);
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
