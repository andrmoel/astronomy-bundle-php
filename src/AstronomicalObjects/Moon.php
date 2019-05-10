<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects;

use Andrmoel\AstronomyBundle\Calculations\MoonCalc;
use Andrmoel\AstronomyBundle\Calculations\SunCalc;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalSphericalCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\LocalHorizontalCoordinates;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;
use Andrmoel\AstronomyBundle\Utils\DistanceUtil;

class Moon extends AstronomicalObject
{
    public function getGeocentricEclipticalSphericalCoordinates(): GeocentricEclipticalSphericalCoordinates
    {
        $T = $this->T;

        $lon = MoonCalc::getLongitude($T);
        $lat = MoonCalc::getLatitude($T);
        $radiusVector = DistanceUtil::km2au(MoonCalc::getDistanceToEarth($T));

        return new GeocentricEclipticalSphericalCoordinates($lon, $lat, $radiusVector);
    }

    public function getGeocentricEquatorialCoordinates(): GeocentricEquatorialCoordinates
    {
        return $this
            ->getGeocentricEclipticalSphericalCoordinates()
            ->getGeocentricEquatorialCoordinates($this->toi);
    }

    public function getLocalHorizontalCoordinates(Location $location): LocalHorizontalCoordinates
    {
        return $this
            ->getGeocentricEquatorialCoordinates()
            ->getLocalHorizontalCoordinates($location, $this->toi);
    }

    public function getIlluminatedFraction(): float
    {
        $T = $this->T;

        $geoEquCoordinatesMoon = $this->getGeocentricEquatorialCoordinates();

        $aMoon = $geoEquCoordinatesMoon->getRightAscension();
        $dMoon = $geoEquCoordinatesMoon->getDeclination();
        $distMoon = MoonCalc::getDistanceToEarth($T);

        $sun = new Sun($this->toi);
        $geoEquCoordinatesSun = $sun->getGeocentricEquatorialCoordinates();
        $aSun = $geoEquCoordinatesSun->getRightAscension();
        $dSun = $geoEquCoordinatesSun->getDeclination();
        $distSun = SunCalc::getDistanceToEarth($T);

        $aMoon = deg2rad($aMoon);
        $aSun = deg2rad($aSun);
        $dMoon = deg2rad($dMoon);
        $dSun = deg2rad($dSun);

        $phi = acos(sin($dSun) * sin($dMoon) + cos($dSun) * cos($dMoon) * cos($aSun - $aMoon));
        $i = atan(($distSun * sin($phi)) / ($distMoon - $distSun * cos($phi)));

        // i must be between 0° and 180°
        $i = rad2deg($i);
        $i = AngleUtil::normalizeAngle($i, 180);
        $i = deg2rad($i);

        $k = (1 + cos($i)) / 2;

        return $k;
    }

    public function isWaxingMoon(): bool
    {
        $dateTimeFuture = clone $this->toi->getDateTime();
        $dateTimeFuture->add(new \DateInterval('PT1S'));

        $illuminatedFraction1 = $this->getIlluminatedFraction();

        $toi = new TimeOfInterest($dateTimeFuture);
        $moon = new Moon($toi);
        $illuminatedFraction2 = $moon->getIlluminatedFraction();


        return $illuminatedFraction2 > $illuminatedFraction1;
    }

    public function getPositionAngleOfMoonsBrightLimb(): float
    {
        $sun = new Sun($this->toi);

        $geoEquCoordinatesMoon = $this->getGeocentricEquatorialCoordinates();
        $geoEquCoordinatesSun = $sun->getGeocentricEquatorialCoordinates();

        $aMoon = $geoEquCoordinatesMoon->getRightAscension();
        $dMoon = $geoEquCoordinatesMoon->getDeclination();
        $aMoonRad = deg2rad($aMoon);
        $dMoonRad = deg2rad($dMoon);

        $aSun = $geoEquCoordinatesSun->getRightAscension();
        $dSun = $geoEquCoordinatesSun->getDeclination();
        $aSunRad = deg2rad($aSun);
        $dSunRad = deg2rad($dSun);

        // Meeus 48.5
        $numerator = cos($dSunRad) * sin($aSunRad - $aMoonRad);
        $denominator = sin($dSunRad) * cos($dMoonRad) - cos($dSunRad) * sin($dMoonRad) * cos($aSunRad - $aMoonRad);

        $x = rad2deg(atan($numerator / $denominator));
        $x = AngleUtil::normalizeAngle($x);

        return $x;
    }

    public function getUpperCulmination(Location $location): TimeOfInterest
    {
        // TODO Implement
        return new TimeOfInterest();
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
