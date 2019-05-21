<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\Coordinates\HeliocentricEclipticalRectangularCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\HeliocentricEclipticalSphericalCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\HeliocentricEquatorialRectangularCoordinates;
use Andrmoel\AstronomyBundle\TimeOfInterest;

interface PlanetInterface
{
    public function setTimeOfInterest(TimeOfInterest $toi): void;

    public function getTimeOfInterest(): TimeOfInterest;

    public function getHeliocentricEclipticalRectangularCoordinates(): HeliocentricEclipticalRectangularCoordinates;

    public function getHeliocentricEclipticalSphericalCoordinates(): HeliocentricEclipticalSphericalCoordinates;

    public function getHeliocentricEquatorialRectangularCoordinates(): HeliocentricEquatorialRectangularCoordinates;

    public function getDistanceToEarthInAu(): float;

    public function getDistanceToEarthInKm(): float;
}
