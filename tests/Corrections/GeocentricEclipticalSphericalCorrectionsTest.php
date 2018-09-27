<?php

namespace Andrmoel\AstronomyBundle\Tests\Corrections;

use Andrmoel\AstronomyBundle\Coordinates\GeocentricEclipticalSphericalCoordinates;
use Andrmoel\AstronomyBundle\Corrections\GeocentricEclipticalSphericalCorrections;

class GeocentricEclipticalSphericalCorrectionsTest
{
    // TODO Write...
    public function testFoo()
    {
        $geoEclSphCoordinates = new GeocentricEclipticalSphericalCoordinates($lon, $lat, $radiusVector);
        $corrections = new GeocentricEclipticalSphericalCorrections($toi);
    }
}
