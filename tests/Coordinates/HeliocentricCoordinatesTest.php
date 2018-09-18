<?php

namespace Andrmoel\AstronomyBundle\Tests\Coordinates;

use Andrmoel\AstronomyBundle\Coordinates\HeliocentricCoordinates;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use PHPUnit\Framework\TestCase;

class HeliocentricCoordinatesTest extends TestCase
{
    /**
     * Meeus 33.a
     */
    public function XtestGetEclipticalCoordinates()
    {
        $L = 26.11428;
        $B = -2.62070;
        $R = 0.724603;

        $toi = new TimeOfInterest(new \DateTime('1992-12-20 00:00:00'));

        $heliocentricCoordinates = new HeliocentricCoordinates($L, $B, $R);
        $eclipticalCoordinates = $heliocentricCoordinates->getEclipticalCoordinates($toi);

        $latitude = $eclipticalCoordinates->getLatitude();
        $longitude = $eclipticalCoordinates->getLongitude();

        // TODO ...
        var_dump($latitude, $longitude);
    }
}
