<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Utils\AngleUtil;

class RectangularGeocentricEquatorialCoordinates
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

    // TODO Test...
    public function getEclipticalCoordinates(): EclipticalCoordinates
    {
        // Meeus 33.2
        $longitude = atan2($this->y, $this->x);
        $longitude = rad2deg($longitude);
        $longitude = AngleUtil::normalizeAngle($longitude);
        $latitude = atan($this->z / sqrt(pow($this->x, 2) + pow($this->y, 2)));
        $latitude = rad2deg($latitude);

        return new EclipticalCoordinates($latitude, $longitude);
    }
}
