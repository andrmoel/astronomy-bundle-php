<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Earth;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

class HeliocentricEclipticalRectangularCoordinates
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

    public function getHeliocentricEclipticalSphericalCoordinates(): HeliocentricEclipticalSphericalCoordinates
    {
        // Meeus 33.2
        $longitude = atan($this->Y / $this->X);
        $longitude = rad2deg($longitude);
        $longitude = AngleUtil::normalizeAngle($longitude);

        $latitude = atan($this->Z / sqrt(pow($this->X, 2) + pow($this->Y, 2)));
        $latitude = rad2deg($latitude);

        $radiusVector = sqrt(pow($this->X, 2) + pow($this->Y, 2) + pow($this->Z, 2));

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

        $X = $this->X - $X0;
        $Y = $this->Y - $Y0;
        $Z = $this->Z - $Z0;

        return new GeocentricEclipticalRectangularCoordinates($X, $Y, $Z);
    }
}
