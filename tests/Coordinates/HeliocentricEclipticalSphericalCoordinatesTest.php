<?php

namespace Andrmoel\AstronomyBundle\Tests\Coordinates;

use Andrmoel\AstronomyBundle\Coordinates\HeliocentricEclipticalSphericalCoordinates;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use PHPUnit\Framework\TestCase;

class HeliocentricEclipticalSphericalCoordinatesTest extends TestCase
{
    public function testGetHeliocentricEclipticalRectangularCoordinates()
    {
        $L = 313.08102;
        $B = -2.08474;
        $R = 0.910947;

        $hcEclSphCoordinates = new HeliocentricEclipticalSphericalCoordinates($L, $B, $R);
        $hcEclRecCoordinates = $hcEclSphCoordinates->getHeliocentricEclipticalRectangularCoordinates();

        $X = $hcEclRecCoordinates->getX();
        $Y = $hcEclRecCoordinates->getY();
        $Z = $hcEclRecCoordinates->getZ();

        $this->assertEquals(0.621794, round($X, 6));
        $this->assertEquals(-0.664905, round($Y, 6));
        $this->assertEquals(-0.033138, round($Z, 6));
    }

    /**
     * Meeus 33.a
     */
    public function testGetGeocentricEclipticalRectangularCoordinates()
    {
        $L = 26.11428;
        $B = -2.62070;
        $R = 0.724603;

        $toi = new TimeOfInterest(new \DateTime('1992-12-20 00:00:00'));

        $hcEclSphCoordinates = new HeliocentricEclipticalSphericalCoordinates($L, $B, $R);
        $geoEquRecCoordinates = $hcEclSphCoordinates->getGeocentricEclipticalRectangularCoordinates($toi);

        $X = $geoEquRecCoordinates->getX();
        $Y = $geoEquRecCoordinates->getY();
        $Z = $geoEquRecCoordinates->getZ();

        $this->assertEquals(0.621746, round($X, 6));
        $this->assertEquals(-0.664810, round($Y, 6));
        $this->assertEquals(-0.033134, round($Z, 6));
    }

    /**
     * Meeus 33.a
     */
    public function testGetGeocentricEclipticalSphericalCoordinates()
    {
        $L = 26.11428;
        $B = -2.62070;
        $R = 0.724603;

        $toi = new TimeOfInterest(new \DateTime('1992-12-20 00:00:00'));

        $hcEclSphCoordinates = new HeliocentricEclipticalSphericalCoordinates($L, $B, $R);
        $geoEclSphCoordinates = $hcEclSphCoordinates->getGeocentricEclipticalSphericalCoordinates($toi);

        $latitude = $geoEclSphCoordinates->getLatitude();
        $longitude = $geoEclSphCoordinates->getLongitude();

        $this->assertEquals(-2.08473, round($latitude, 5));
        $this->assertEquals(313.08289, round($longitude, 5));
    }
}
