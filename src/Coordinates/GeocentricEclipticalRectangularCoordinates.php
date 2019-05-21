<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Calculations\EarthCalc;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

class GeocentricEclipticalRectangularCoordinates
{
    private $X = 0;
    private $Y = 0;
    private $Z = 0;

    public function __construct(float $X, float $Y, float $Z)
    {
        $this->X = $X;
        $this->Y = $Y;
        $this->Z = $Z;
    }

    public function getX(): float
    {
        return $this->X;
    }

    public function getY(): float
    {
        return $this->Y;
    }

    public function getZ(): float
    {
        return $this->Z;
    }

    public function getGeocentricEquatorialRectangularCoordinates(float $T): GeocentricEquatorialRectangularCoordinates
    {

    }

    public function getGeocentricEquatorialSphericalCoordinates(float $T): GeocentricEquatorialSphericalCoordinates
    {

    }

    // TODO
    public function getLocalHorizontalCoordinates(Location $location, TimeOfInterest $toi): LocalHorizontalCoordinates
    {
        return new LocalHorizontalCoordinates(0, 0);
    }
}
