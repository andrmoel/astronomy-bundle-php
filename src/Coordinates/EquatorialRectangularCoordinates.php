<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\Utils\AngleUtil;

class EquatorialRectangularCoordinates
{
    private $X = 0.0;
    private $Y = 0.0;
    private $Z = 0.0;

    // TODO Sind es nicht eher Ecliptical rectangular coordinates???
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

    // TODO Write tests...
    public function getEclipticalCoordinates(): EclipticalCoordinates
    {
        // Meeus 33.2
        $longitude = atan($this->Y / $this->X);
        $longitude = rad2deg($longitude);
        $longitude = AngleUtil::normalizeAngle($longitude);
        $latitude = atan($this->Z / sqrt(pow($this->X, 2) + pow($this->Y, 2)));
        $latitude = rad2deg($latitude);

        // TODO Corrections of light time and aberration...

        return new EclipticalCoordinates($latitude, $longitude);
    }
}
