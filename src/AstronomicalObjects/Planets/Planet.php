<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\AstronomicalObjects\AstronomicalObject;
use Andrmoel\AstronomyBundle\Calculations\TimeCalc;
use Andrmoel\AstronomyBundle\Calculations\VSOP87\VSOP87Interface;
use Andrmoel\AstronomyBundle\Calculations\VSOP87Calc;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalRectangularCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalSphericalCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialRectangularCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialSphericalCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\HeliocentricEclipticalRectangularCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\HeliocentricEclipticalSphericalCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\HeliocentricEquatorialRectangularCoordinates;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;
use Andrmoel\AstronomyBundle\Utils\DistanceUtil;

abstract class Planet extends AstronomicalObject implements PlanetInterface
{
    /** @var VSOP87Interface */
    protected $VSOP87_SPHERICAL;

    /** @var VSOP87Interface */
    protected $VSOP87_RECTANGULAR;

    public function getHeliocentricEclipticalRectangularCoordinates(): HeliocentricEclipticalRectangularCoordinates
    {
        $t = $this->toi->getJulianMillenniaFromJ2000();
        $coefficients = VSOP87Calc::solve($this->VSOP87_RECTANGULAR, $t);

        $x = $coefficients[0];
        $y = $coefficients[1];
        $z = $coefficients[2];

        return new HeliocentricEclipticalRectangularCoordinates($x, $y, $z);
    }

    public function getHeliocentricEclipticalSphericalCoordinates(): HeliocentricEclipticalSphericalCoordinates
    {
        $t = $this->toi->getJulianMillenniaFromJ2000();
        $coefficients = VSOP87Calc::solve($this->VSOP87_SPHERICAL, $t);

        $L = $coefficients[0];
        $B = $coefficients[1];
        $R = $coefficients[2];

        $L = AngleUtil::normalizeAngle(rad2deg($L));
        $B = rad2deg($B);

        return new HeliocentricEclipticalSphericalCoordinates($B, $L, $R);
    }

    // TODO
    public function getHeliocentricEquatorialRectangularCoordinates(): HeliocentricEquatorialRectangularCoordinates
    {
        return new HeliocentricEquatorialRectangularCoordinates(0, 0, 0);
    }

    public function getGeocentricEclipticalRectangularCoordinates(): GeocentricEclipticalRectangularCoordinates
    {
        $helEclRecCoordPlanet = $this->getHeliocentricEclipticalRectangularCoordinates();

        $earth = new Earth($this->toi);
        $helEclRecCoordEarth = $earth->getHeliocentricEclipticalRectangularCoordinates();

        $x = $helEclRecCoordPlanet->getX() - $helEclRecCoordEarth->getX();
        $y = $helEclRecCoordPlanet->getY() - $helEclRecCoordEarth->getY();
        $z = $helEclRecCoordPlanet->getZ() - $helEclRecCoordEarth->getZ();

        return new GeocentricEclipticalRectangularCoordinates($x, $y, $z);
    }

    // TODO
    public function getGeocentricEclipticalSphericalCoordinates(): GeocentricEclipticalSphericalCoordinates
    {
        return $this->getGeocentricEclipticalRectangularCoordinates()
            ->getGeocentricEclipticalSphericalCoordinates();

        // TODO Calculate apparent

        $geoEclRecCoord = $this->getGeocentricEclipticalRectangularCoordinates();

        $x = $geoEclRecCoord->getX();
        $y = $geoEclRecCoord->getY();
        $z = $geoEclRecCoord->getZ();
        var_dump($x, $y, $z);die();

        $d = sqrt(pow($x, 2) + pow($y, 2) + pow($z, 2));

        // Light time correction
        $tau = 0.0057755183 * $d;

        // Repeat
        $JD = $this->toi->getJulianDay() - $tau;
        $t = TimeCalc::getJulianCenturiesFromJ2000($JD) / 10;

        $coefficients = VSOP87Calc::solve($this->VSOP87_SPHERICAL, $t);

        $lat = rad2deg($coefficients[1]);

        $lon = rad2deg($coefficients[0]);
        $lon = AngleUtil::normalizeAngle($lon);
        var_dump($lat, $lon);die();


        $latRad = atan($z / sqrt(pow($x, 2) + pow($y, 2)));
        $lat = rad2deg($latRad);

        $lon = atan2($y, $x);
        $lon = AngleUtil::normalizeAngle(rad2deg($lon));

        // Correction
//        $Ls = $lon - 1.397 * $T - 0.00031 * pow($T, 2);
//        $LsRad = deg2rad($Ls);
//
//        $dL = AngleUtil::angle2dec('-0°0\'0.0933"')
//            + AngleUtil::angle2dec('0°0\'0.03916"') * (cos($LsRad) + sin($LsRad)) * tan($latRad);


        var_dump($Ls, $dL);die();

        return new GeocentricEclipticalSphericalCoordinates($lat, $lon, $d);
    }

    public function getGeocentricEquatorialRectangularCoordinates(): GeocentricEquatorialRectangularCoordinates
    {
        return new GeocentricEquatorialRectangularCoordinates($x, $y, $z);
    }

    // TODO
    public function getGeocentricEquatorialSphericalCoordinates(): GeocentricEquatorialSphericalCoordinates
    {
        return new GeocentricEquatorialSphericalCoordinates(0, 0, 0);
    }

    public function getDistanceToEarthInAu(): float
    {
        $geoEclRecCoord = $this->getGeocentricEquatorialRectangularCoordinates();

        $x = $geoEclRecCoord->getX();
        $y = $geoEclRecCoord->getY();
        $z = $geoEclRecCoord->getZ();

        $d = sqrt($x * $x + $y * $y + $z * $z);

        return $d;
    }

    public function getDistanceToEarthInKm(): float
    {
        $d = $this->getDistanceToEarthInAu();

        return DistanceUtil::au2km($d);
    }

    // ------------------

    public function test()
    {
        $geoEquRecCoord = $this->getGeocentricEquatorialRectangularCoordinates();

        var_dump($geoEquRecCoord);
    }

    /**
     * The apparent position is light-time corrected
     * @return HeliocentricEclipticalRectangularCoordinates
     */
    public function getApparentHeliocentricEclipticalRectangularCoordinates(): HeliocentricEclipticalRectangularCoordinates
    {
        return $this->getApparentHeliocentricEclipticalSphericalCoordinates()
            ->getHeliocentricEclipticalRectangularCoordinates();
    }

//    /**
//     * The apparent position is light-time corrected
//     * @return HeliocentricEclipticalSphericalCoordinates
//     */
//    public function getApparentHeliocentricEclipticalSphericalCoordinates(): HeliocentricEclipticalSphericalCoordinates
//    {
//        // First we need to calculate the distance between the planet and the earth.
//        // With the formula Meeus 33.3 we can calculated the light-time corrected position of the planet.
//        $t = $this->toi->getJulianMillenniaFromJ2000();
//
//        $geoEclSphCoordinates = $this->getHeliocentricEclipticalSphericalCoordinates($t)
//            ->getGeocentricEclipticalSphericalCoordinates();
//
//        $distance = $geoEclSphCoordinates->getRadiusVector();
//        $toiCorrected = $this->toi->getTimeOfInterestLightTimeCorrected($distance);
//
//        // With the corrected time, we can calculate the true helopcentric position.
//        $t = $toiCorrected->getJulianMillenniaFromJ2000();
//
//        return $this->getHeliocentricEclipticalSphericalCoordinates($t);
//    }
}
