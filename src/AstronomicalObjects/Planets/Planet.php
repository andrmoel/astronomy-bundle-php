<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\AstronomicalObjects\AstronomicalObject;
use Andrmoel\AstronomyBundle\Calculations\VSOP87\VSOP87Interface;
use Andrmoel\AstronomyBundle\Calculations\VSOP87Calc;
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

    // TODO
    public function getGeocentricEclipticalSphericalCoordinates(): GeocentricEclipticalSphericalCoordinates
    {
        return new GeocentricEclipticalSphericalCoordinates(0, 0, 0);
    }

    public function getGeocentricEquatorialRectangularCoordinates(): GeocentricEquatorialRectangularCoordinates
    {
        $earth = new Earth($this->toi);

        $helEclRecCoordPlanet = $this->getHeliocentricEclipticalRectangularCoordinates();
        $helEclRecCoordEarth = $earth->getHeliocentricEclipticalRectangularCoordinates();

        $x = $helEclRecCoordPlanet->getX() - $helEclRecCoordEarth->getX();
        $y = $helEclRecCoordPlanet->getY() - $helEclRecCoordEarth->getY();
        $z = $helEclRecCoordPlanet->getZ() - $helEclRecCoordEarth->getZ();

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
