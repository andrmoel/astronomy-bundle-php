<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Calculations\VSOP87\EarthRectangularVSOP87;
use Andrmoel\AstronomyBundle\Calculations\VSOP87Calc;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

class HeliocentricEclipticalRectangularCoordinates
{
    private $x = 0;
    private $y = 0;
    private $z = 0;

    public function __construct(float $x, float $y, float $z)
    {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    public function getX(): float
    {
        return $this->x;
    }

    public function getY(): float
    {
        return $this->y;
    }

    public function getZ(): float
    {
        return $this->z;
    }

    public function getHeliocentricEclipticalSphericalCoordinates(): HeliocentricEclipticalSphericalCoordinates
    {
        // Meeus 33.2
        $lonRad = atan2($this->y, $this->x);
        $lon = AngleUtil::normalizeAngle(rad2deg($lonRad));

        $latRad = atan($this->z / sqrt(pow($this->x, 2) + pow($this->y, 2)));
        $lat = rad2deg($latRad);

        $r = sqrt(pow($this->x, 2) + pow($this->y, 2) + pow($this->z, 2));

        return new HeliocentricEclipticalSphericalCoordinates($lat, $lon, $r);
    }

    // TODO
    public function getHeliocentricEquatorialRectangularCoordinates(): HeliocentricEquatorialRectangularCoordinates
    {
        return new HeliocentricEquatorialRectangularCoordinates(0, 0, 0);
    }

    public function getGeocentricEclipticalRectangularCoordinates(float $T): GeocentricEclipticalRectangularCoordinates
    {
        $t = $T / 10;

        // Heliocentric coordinates of earth
        $coefficients = VSOP87Calc::solve(EarthRectangularVSOP87::class, $t);

        $X = $this->x - $coefficients[0];
        $Y = $this->y - $coefficients[1];
        $Z = $this->z - $coefficients[2];

        return new GeocentricEclipticalRectangularCoordinates($X, $Y, $Z);
    }

    // TODO
    public function getGeocentricEclipticalSphericalCoordinates(): GeocentricEclipticalSphericalCoordinates
    {
        return new GeocentricEclipticalSphericalCoordinates(0, 0, 0);
    }

    // TODO
    public function getGeocentricEquatorialRectangularCoordinates(): GeocentricEquatorialRectangularCoordinates
    {
        return new GeocentricEquatorialRectangularCoordinates(0, 0, 0);
    }

    // TODO
    public function getGeocentricEquatorialSphericalCoordinates(): GeocentricEquatorialSphericalCoordinates
    {
        return new GeocentricEquatorialSphericalCoordinates(0, 0, 0);
    }
}
