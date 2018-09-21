<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\AstronomicalObjects\AstronomicalObject;
use Andrmoel\AstronomyBundle\Coordinates\HeliocentricEclipticalRectangularCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\HeliocentricEclipticalSphericalCoordinates;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

abstract class Planet extends AstronomicalObject
{
    protected $vsop87 = [];

    abstract function loadVSOP87Data(): array;

    public function __construct(TimeOfInterest $toi = null)
    {
        parent::__construct($toi);
        $this->vsop87 = $this->loadVSOP87Data();
    }

    /**
     * The apparent position is light-time corrected
     * @return HeliocentricEclipticalSphericalCoordinates
     */
    public function getApparentHeliocentricEclipticalSphericalCoordinates(): HeliocentricEclipticalSphericalCoordinates
    {
        // First we need to calculate the distance between the planet and the earth.
        // With the formula Meeus 33.3 we can calculated the light-time corrected position of the planet.
        $t = $this->toi->getJulianMillenniaFromJ2000();

        $geoEclSphCoordinates = $this->getHeliocentricEclipticalSphericalCoordinates($t)
            ->getGeocentricEclipticalSphericalCoordinates($this->toi);

        $distance = $geoEclSphCoordinates->getRadiusVector();
        $toiCorrected = $this->toi->getTimeOfInterestLightTimeCorrected($distance);

        // With the corrected time, we can calculate the true helopcentric position.
        $t = $toiCorrected->getJulianMillenniaFromJ2000();

        return $this->getHeliocentricEclipticalSphericalCoordinates($t);
    }

    public function getHeliocentricEclipticalSphericalCoordinates(
        float $t = null
    ): HeliocentricEclipticalSphericalCoordinates
    {
        $t = $t ? $t : $this->toi->getJulianMillenniaFromJ2000();

        $L = $this->resolveTerms($this->vsop87['L'], $t);
        $L = rad2deg($L);
        $L = AngleUtil::normalizeAngle($L);

        $B = $this->resolveTerms($this->vsop87['B'], $t);
        $B = rad2deg($B);

        $R = $this->resolveTerms($this->vsop87['R'], $t);

        return new HeliocentricEclipticalSphericalCoordinates($L, $B, $R);
    }

    public function getHeliocentricEclipticalRectangularCoordinates(): HeliocentricEclipticalRectangularCoordinates
    {
        return $this->getHeliocentricEclipticalSphericalCoordinates()
            ->getHeliocentricEclipticalRectangularCoordinates();
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

    private function resolveTerms(array $terms, float $t): float
    {
        // Meeus 32.2
        $sum = 0.0;
        foreach ($terms as $key => $arguments) {
            $value = $this->sumUpArguments($arguments, $t);

            $sum += $value * pow($t, $key);
        }

        return $sum;
    }

    private function sumUpArguments(array $arguments, float $t): float
    {
        // Meeus 21.1
        $sum = 0.0;
        foreach ($arguments as $argument) {
            $sum += $argument[0] * cos($argument[1] + $argument[2] * $t);
        }

        return $sum;
    }
}
