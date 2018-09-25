<?php

namespace Andrmoel\AstronomyBundle\Tests\Corrections;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Earth;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Sun;
use Andrmoel\AstronomyBundle\Constants;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalSphericalCoordinates;
use Andrmoel\AstronomyBundle\Corrections\GeocentricEclipticalSphericalCorrections;
use Andrmoel\AstronomyBundle\TimeOfInterest;

class GeocentricEclipticalSphericalCorrectionsTest
{
    // TODO Write...
    public function testFoo()
    {
        $geoEclSphCoordinates = new GeocentricEclipticalSphericalCoordinates($lon, $lat, $radiusVector);
        $corrections = new GeocentricEclipticalSphericalCorrections($toi);
    }
}
