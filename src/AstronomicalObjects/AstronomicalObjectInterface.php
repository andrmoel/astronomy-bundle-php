<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects;

use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialCoordinates;
use Andrmoel\AstronomyBundle\TimeOfInterest;

interface AstronomicalObjectInterface
{
    public function setTimeOfInterest(TimeOfInterest $toi): void;

    public function getGeocentricEquatorialCoordinates(): GeocentricEquatorialCoordinates;
}
