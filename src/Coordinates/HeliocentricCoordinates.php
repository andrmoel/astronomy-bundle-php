<?php

namespace Andrmoel\AstronomyBundle\Coordinates;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Earth;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

class HeliocentricCoordinates
{
    private $L = 0;
    private $B = 0;
    private $R = 0;


    public function __construct(float $eclipticalLongitude, float $eclipticalLatitude, float $radialVector)
    {
        $this->L = $eclipticalLongitude;
        $this->B = $eclipticalLatitude;
        $this->R = $radialVector;
    }


    public function getEclipticalLongitude(): float
    {
        return $this->L;
    }


    public function getEclipticalLatitude(): float
    {
        return $this->B;
    }


    public function getRadiusVector(): float
    {
        return $this->R;
    }

    public function getEclipticalCoordinates(TimeOfInterest $toi): EclipticalCoordinates
    {
        // TODO Logik for X, Y, Z auslagern?
        $LRad = deg2rad($this->L);
        $BRad = deg2rad($this->B);
        $R = $this->R;

        // Heliocentric coordinates of earth
        $earth = new Earth($toi);
        $heliocentricCoordinatesEarth = $earth->getHeliocentricCoordinates();
        $L0 = $heliocentricCoordinatesEarth->getEclipticalLongitude();
        $L0Rad = deg2rad($L0);
        $B0 = $heliocentricCoordinatesEarth->getEclipticalLatitude();
        $B0Rad = deg2rad($B0);
        $R0 = $heliocentricCoordinatesEarth->getRadiusVector();

        // Meeus 33.1
        $x = $R * cos($BRad) * cos($LRad) - $R0 * cos($B0Rad) * cos($L0Rad);
        $y = $R * cos($BRad) * sin($LRad) - $R0 * cos($B0Rad) * sin($L0Rad);
        $z = $R * sin($BRad) - $R0 * sin($B0Rad);

        // TODO ENDE...

        // Meeus 33.2
        $longitude = atan($y / $x);
        $longitude = rad2deg($longitude);
        $longitude = AngleUtil::normalizeAngle($longitude);
        $latitude = atan($z / sqrt(pow($x, 2) + pow($y, 2)));
        $latitude = rad2deg($latitude);

        // TODO Corrections of light time and aberration...

        return new EclipticalCoordinates($latitude, $longitude);
    }
}
