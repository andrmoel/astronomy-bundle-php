<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\AstronomicalObjects\AstronomicalObject;
use Andrmoel\AstronomyBundle\Calculations\VSOP87\VSOP87Interface;
use Andrmoel\AstronomyBundle\Calculations\VSOP87Calc;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalSphericalCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\HeliocentricEclipticalRectangularCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\HeliocentricEclipticalSphericalCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\HeliocentricEquatorialRectangularCoordinates;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

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

    public function test()
    {
        $helEclSphCoord = $this->getHeliocentricEclipticalSphericalCoordinates();

        $B = $helEclSphCoord->getLatitude();
        $L = $helEclSphCoord->getLongitude();
        $R = $helEclSphCoord->getRadiusVector();

        $BRad = deg2rad($B);
        $LRad = deg2rad($L);

        // Meeus 33.1
        $X = $R * cos($BRad) * cos($LRad);
        $Y = $R * cos($BRad) * sin($LRad);
        $Z = $R * sin($BRad);

        // Earth
        $earth = new Earth($this->toi);
        $helEclSphCoord = $earth->getHeliocentricEclipticalSphericalCoordinates();

        $B0 = $helEclSphCoord->getLatitude();
        $L0 = $helEclSphCoord->getLongitude();
        $R0 = $helEclSphCoord->getRadiusVector();

        $B0Rad = deg2rad($B0);
        $L0Rad = deg2rad($L0);

        $X0 = $R0 * cos($B0Rad) * cos($L0Rad);
        $Y0 = $R0 * cos($B0Rad) * sin($L0Rad);
        $Z0 = $R0 * sin($B0Rad);

        $x = $X - $X0;
        $y = $Y - $Y0;
        $z = $Z - $Z0;

        // Meeus 33.2
        $lat = atan($z / (sqrt(pow($x, 2) + pow($y, 2))));
        $lat = rad2deg($lat);
        $lon = atan2($y, $z);
        $lon = AngleUtil::normalizeAngle($lon);

        var_dump($lat, $lon);
        die();

        var_dump($x, $y, $z);
        die();
    }

    public function getGeocentricEclipticalSphericalCoordinates(): GeocentricEclipticalSphericalCoordinates
    {
        $earth = new Earth($this->toi);
//        $geo = $earth->get

        // Meeus 33.2
        $lat = atan2($y, $x);

        new GeocentricEclipticalSphericalCoordinates($lat, $lon, $R);
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
