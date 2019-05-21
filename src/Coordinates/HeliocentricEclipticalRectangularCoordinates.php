<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Earth;
use Andrmoel\AstronomyBundle\TimeOfInterest;
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

    public function getHeliocentricEquatorialRectangularCoordinates(): HeliocentricEquatorialRectangularCoordinates
    {
        // TODO
        return new HeliocentricEquatorialRectangularCoordinates(0, 0, 0);
    }

    public function getGeocentricEclipticalSphericalCoordinates(): GeocentricEclipticalSphericalCoordinates
    {
        // TODO
        return new GeocentricEclipticalSphericalCoordinates(0, 0, 0);
    }

    public function getGeocentricEquatorialRectangularCoordinates(
        TimeOfInterest $toi
    ): GeocentricEquatorialRectangularCoordinates
    {
        // Heliocentric coordinates of earth
        $earth = new Earth($toi);
        $hcEclRecCoordinatesEarth = $earth->getHeliocentricEclipticalRectangularCoordinates();

        $X0 = $hcEclRecCoordinatesEarth->getX();
        $Y0 = $hcEclRecCoordinatesEarth->getY();
        $Z0 = $hcEclRecCoordinatesEarth->getZ();

        $X = $this->x - $X0;
        $Y = $this->y - $Y0;
        $Z = $this->z - $Z0;

        return new GeocentricEquatorialRectangularCoordinates($X, $Y, $Z);
    }

    public function getGeocentricEquatorialSphericalCoordinates(): GeocentricEquatorialSphericalCoordinates
    {
        // TODO
        return new GeocentricEquatorialSphericalCoordinates(0, 0, 0);
    }
}
