<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\AstronomicalObjects\AstronomicalObject;
use Andrmoel\AstronomyBundle\Coordinates\HeliocentricEclipticalRectangularCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\HeliocentricEclipticalSphericalCoordinates;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

abstract class Planet extends AstronomicalObject
{
    // Meeus Appendix III for planets
    protected $argumentsL = null;
    protected $argumentsB = null;
    protected $argumentsR = null;

    public function getHeliocentricEclipticalSphericalCoordinates(): HeliocentricEclipticalSphericalCoordinates
    {
        $t = $this->toi->getJulianMillenniaFromJ2000();

        $L = $this->resolveTerms($this->argumentsL, $t);
        $L = rad2deg($L);
        $L = AngleUtil::normalizeAngle($L);

        $B = $this->resolveTerms($this->argumentsB, $t);
        $B = rad2deg($B);

        $R = $this->resolveTerms($this->argumentsR, $t);

        return new HeliocentricEclipticalSphericalCoordinates($L, $B, $R);
    }

    public function getHeliocentricEclipticalSphericalCoordinatesLightTimeCorrected(): HeliocentricEclipticalSphericalCoordinates
    {
        $geoEclSphCoordinates = $this->getHeliocentricEclipticalSphericalCoordinates()
            ->getGeocentricEclipticalSphericalCoordinates($this->toi);

        $distance = $geoEclSphCoordinates->getRadiusVector();
        $toiCorrected = $this->toi->getTimeOfInterestLightTimeCorrected($distance);

        $t = $toiCorrected->getJulianMillenniaFromJ2000();

        $L = $this->resolveTerms($this->argumentsL, $t);
        $L = rad2deg($L);
        $L = AngleUtil::normalizeAngle($L);

        $B = $this->resolveTerms($this->argumentsB, $t);
        $B = rad2deg($B);

        $R = $this->resolveTerms($this->argumentsR, $t);

        return new HeliocentricEclipticalSphericalCoordinates($L, $B, $R);
    }

    public function getHeliocentricEclipticalRectangularCoordinates(): HeliocentricEclipticalRectangularCoordinates
    {
        return $this->getHeliocentricEclipticalSphericalCoordinates()
            ->getHeliocentricEclipticalRectangularCoordinates();
    }

    protected function resolveTerms(array $terms, float $t): float
    {
        // Meeus 32.2
        $sum = 0.0;
        foreach ($terms as $key => $arguments) {
            $value = $this->sumUpArguments($arguments, $t);

            $sum += $value * pow($t, $key);
        }

        $sum /= pow(10, 8);

        return $sum;
    }

    protected function sumUpArguments(array $arguments, float $t): float
    {
        // Meeus 21.1
        $sum = 0.0;
        foreach ($arguments as $argument) {
            $sum += $argument[0] * cos($argument[1] + $argument[2] * $t);
        }

        return $sum;
    }
}
