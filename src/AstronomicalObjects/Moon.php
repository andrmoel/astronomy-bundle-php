<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects;

use Andrmoel\AstronomyBundle\Calculations\EarthCalc;
use Andrmoel\AstronomyBundle\Calculations\MoonCalc;
use Andrmoel\AstronomyBundle\Calculations\SunCalc;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalSphericalCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialRectangularCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialSphericalCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\LocalHorizontalCoordinates;
use Andrmoel\AstronomyBundle\Corrections\LocalHorizontalCorrections;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;
use Andrmoel\AstronomyBundle\Utils\DistanceUtil;

class Moon extends AstronomicalObject implements AstronomicalObjectInterface
{
    public function getGeocentricEclipticalSphericalCoordinates(): GeocentricEclipticalSphericalCoordinates
    {
        $T = $this->T;

        $lon = MoonCalc::getLongitude($T);
        $lat = MoonCalc::getLatitude($T);
        $radiusVector = DistanceUtil::km2au(MoonCalc::getDistanceToEarth($T));

        // Corrections
        $dPhi = EarthCalc::getNutationInLongitude($T);
        $lon = $lon + $dPhi;

        return new GeocentricEclipticalSphericalCoordinates($lon, $lat, $radiusVector);
    }

    public function getGeocentricEquatorialRectangularCoordinates(): GeocentricEquatorialRectangularCoordinates
    {
        return $this
            ->getGeocentricEclipticalSphericalCoordinates()
            ->getGeocentricEquatorialRectangularCoordinates($this->T);
    }

    public function getGeocentricEquatorialSphericalCoordinates(): GeocentricEquatorialSphericalCoordinates
    {
        return $this
            ->getGeocentricEclipticalSphericalCoordinates()
            ->getGeocentricEquatorialSphericalCoordinates($this->T);
    }

    public function getLocalHorizontalCoordinates(Location $location, bool $refraction = true): LocalHorizontalCoordinates
    {
        $locHorCoord = $this
            ->getGeocentricEquatorialSphericalCoordinates()
            ->getLocalHorizontalCoordinates($location, $this->T);

        if ($refraction) {
            $locHorCoord = LocalHorizontalCorrections::correctAtmosphericRefraction($locHorCoord);
        }

        return $locHorCoord;
    }

    /**
     * Distance to earth [km]
     * @return float
     */
    public function getDistanceToEarth(): float
    {
        $d = MoonCalc::getDistanceToEarth($this->T);

        return $d;
    }

    public function getIlluminatedFraction(): float
    {
        $T = $this->T;

        $geoEquCoordinatesMoon = $this->getGeocentricEquatorialSphericalCoordinates();

        $aMoon = $geoEquCoordinatesMoon->getRightAscension();
        $dMoon = $geoEquCoordinatesMoon->getDeclination();
        $distMoon = MoonCalc::getDistanceToEarth($T);

        $sun = new Sun($this->toi);
        $geoEquCoordinatesSun = $sun->getGeocentricEquatorialSphericalCoordinates();
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
        $illuminatedFraction1 = $this->getIlluminatedFraction();

        $JDFuture = $this->toi->getJulianDay() + 1; // Tomorrow
        $toiFuture = TimeOfInterest::createFromJulianDay($JDFuture);

        $moon = new Moon($toiFuture);
        $illuminatedFraction2 = $moon->getIlluminatedFraction();


        return $illuminatedFraction2 > $illuminatedFraction1;
    }

    public function getPositionAngleOfMoonsBrightLimb(): float
    {
        $sun = new Sun($this->toi);

        $geoEquCoordinatesMoon = $this->getGeocentricEquatorialSphericalCoordinates();
        $geoEquCoordinatesSun = $sun->getGeocentricEquatorialSphericalCoordinates();

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

    /**
     * @param Location $location
     * @return TimeOfInterest
     * @deprecated Not yet implemented
     */
    public function getUpperCulmination(Location $location): TimeOfInterest
    {
        // TODO Implement
        return TimeOfInterest::createFromCurrentTime();
    }

    /**
     * @param Location $location
     * @return TimeOfInterest
     * @deprecated Not yet implemented
     */
    public function getMoonrise(Location $location): TimeOfInterest
    {
        // TODO Implement
        return TimeOfInterest::createFromCurrentTime();
    }

    /**
     * @param Location $location
     * @return TimeOfInterest
     * @deprecated Not yet implemented
     */
    public function getMoonset(Location $location): TimeOfInterest
    {
        // TODO Implement
        return TimeOfInterest::createFromCurrentTime();
    }
}
