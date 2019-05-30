<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects;

use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalSphericalCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialRectangularCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialSphericalCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\LocalHorizontalCoordinates;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;

interface AstronomicalObjectInterface
{
    public function setTimeOfInterest(TimeOfInterest $toi): void;

    public function getTimeOfInterest(): TimeOfInterest;

    public function getGeocentricEclipticalSphericalCoordinates(): GeocentricEclipticalSphericalCoordinates;

    public function getGeocentricEquatorialRectangularCoordinates(): GeocentricEquatorialRectangularCoordinates;

    public function getGeocentricEquatorialSphericalCoordinates(): GeocentricEquatorialSphericalCoordinates;

    public function getLocalHorizontalCoordinates(Location $location, bool $refraction = true): LocalHorizontalCoordinates;

    public function getDistanceToEarth(): float;
}
