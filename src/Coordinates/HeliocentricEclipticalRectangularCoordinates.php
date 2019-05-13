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
        $longitude = atan($this->y / $this->x);
        $longitude = rad2deg($longitude);
        $longitude = AngleUtil::normalizeAngle($longitude);

        $latitude = atan($this->z / sqrt(pow($this->x, 2) + pow($this->y, 2)));
        $latitude = rad2deg($latitude);

        $radiusVector = sqrt(pow($this->x, 2) + pow($this->y, 2) + pow($this->z, 2));

        return new HeliocentricEclipticalSphericalCoordinates($longitude, $latitude, $radiusVector);
    }

    public function getGeocentricEclipticalRectangularCoordinates(
        TimeOfInterest $toi
    ): GeocentricEclipticalRectangularCoordinates
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

        return new GeocentricEclipticalRectangularCoordinates($X, $Y, $Z);
    }
}
