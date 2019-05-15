<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects;

use Andrmoel\AstronomyBundle\Calculations\EarthCalc;
use Andrmoel\AstronomyBundle\Calculations\SunCalc;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Earth;
use Andrmoel\AstronomyBundle\Calculations\TimeCalc;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalSphericalCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialRectangularCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialSphericalCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\LocalHorizontalCoordinates;
use Andrmoel\AstronomyBundle\Corrections\GeocentricEquatorialCorrections;
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

        return new GeocentricEclipticalSphericalCoordinates($lat, $lon, $R);
    }

    public function getGeocentricEquatorialRectangularCoordinates(): GeocentricEquatorialRectangularCoordinates
    {
        $T = $this->T;

        // TODO ::::::
        $earth = new Earth($this->toi);
        $helEclSphCoord = $earth->getHeliocentricEclipticalSphericalCoordinates();
        $geoEquSphCoord = $helEclSphCoord->getGeocentricEquatorialRectangularCoordinates($this->toi);

// TODO ..........
        $geoEclSphCoord = $this->getGeocentricEclipticalSphericalCoordinates();

        $R = SunCalc::getRadiusVector($T);
        $eps0 = EarthCalc::getMeanObliquityOfEcliptic($T);

        // True longitude
        $lat = $geoEclSphCoord->getLatitude();
        $lon = $geoEclSphCoord->getLongitude();

        // TODO Calculate
        $R = 0.99760775;
        $lat = AngleUtil::angle2dec('0°0\'0.62"');
        $lon = 199.907347;

        $epsRad = deg2rad($eps0);
        $latRad = deg2rad($lat);
        $lonRad = deg2rad($lon);

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

    public function getUpperCulmination(Location $location): TimeOfInterest
    {
        $jd0 = $this->toi->getJulianDay0();
        $lon = $location->getLongitude();

        $jd = $jd0 - $lon / 360;

        $Tnoon = TimeCalc::getJulianCenturiesFromJ2000($jd);
        $equationOfTime = EarthCalc::getEquationOfTimeInMinutes($Tnoon);

        $solNoonOffset = 720 - ($lon * 4) - $equationOfTime; // in minutes
        $Tnew = TimeCalc::getJulianCenturiesFromJ2000($jd + $solNoonOffset / 1440);
        $equationOfTime = EarthCalc::getEquationOfTimeInMinutes($Tnew);

        $solNoonLocal = 720 - ($lon * 4) - $equationOfTime;

        $jd = $jd0 + $solNoonLocal / 1440;

        $toi = new TimeOfInterest();
        $toi->setJulianDay($jd);

        return $toi;
    }

    public function getSunrise(Location $location): TimeOfInterest
    {
        // TODO Implement
        return new TimeOfInterest();
    }

    public function getSunset(Location $location): TimeOfInterest
    {
        // TODO Implement
        return new TimeOfInterest();
    }
}
