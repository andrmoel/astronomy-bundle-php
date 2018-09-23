<?php

namespace Andrmoel\AstronomyBundle\Tests\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Earth;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Venus;
use Andrmoel\AstronomyBundle\Corrections\GeocentricEclipticalSphericalCorrections;
use Andrmoel\AstronomyBundle\Corrections\GeocentricEquatorialCorrections;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;
use PHPUnit\Framework\TestCase;

class VenusTest extends TestCase
{
    /**
     * Meeus 33.a
     */
    public function XtestGetHeliocentricEclipticalSphericalCoordinates()
    {
        $toi = new TimeOfInterest(new \DateTime('1992-12-20 00:00:00'));
        $venus = new Venus($toi);

        $helEclSphCoordinates = $venus->getHeliocentricEclipticalSphericalCoordinates();

        $L = $helEclSphCoordinates->getLongitude();
        $B = $helEclSphCoordinates->getLatitude();
        $R = $helEclSphCoordinates->getRadiusVector();

        $this->assertEquals(26.11412, round($L, 5));
        $this->assertEquals(-2.62060, round($B, 5));
        $this->assertEquals(0.724602, round($R, 6));
    }

    /**
     * Meeus 33.a
     */
    public function XtestGetApparentHeliocentricEclipticalSphericalCoordinates()
    {
        $toi = new TimeOfInterest(new \DateTime('1992-12-20 00:00:00'));
        $venus = new Venus($toi);

        $helEclSphCoordinates = $venus->getApparentHeliocentricEclipticalSphericalCoordinates();

        $L = $helEclSphCoordinates->getLongitude();
        $B = $helEclSphCoordinates->getLatitude();
        $R = $helEclSphCoordinates->getRadiusVector();

        $this->assertEquals(26.10571, round($L, 5));
        $this->assertEquals(-2.62092, round($B, 5));
        $this->assertEquals(0.724602, round($R, 6));
    }

    /**
     * Meeus 33.a
     */
    public function test()
    {
        $toi = new TimeOfInterest(new \DateTime('1992-12-20 00:00:00'));
        $venus = new Venus($toi);

//        // TODO Test...
//        $geoEclSphCoordinates = $venus->getApparentHeliocentricEclipticalSphericalCoordinates()
//            ->getGeocentricEclipticalSphericalCoordinates($toi);
//
//        $corrections = new GeocentricEclipticalSphericalCorrections($toi);
//        $geoEclSphCoordinates = $corrections->correctCoordinates($geoEclSphCoordinates);
//
//        $geoEqaCoordinates = $geoEclSphCoordinates->getGeocentricEquatorialCoordinates($toi);
//
//        $ra = $geoEqaCoordinates->getRightAscension();
//        $d = $geoEqaCoordinates->getDeclination();
//
//        var_dump(AngleUtil::dec2time($ra), AngleUtil::dec2angle($d));die();

        // TODO ... 2
        $geoEqaCoordinates = $venus->getApparentHeliocentricEclipticalSphericalCoordinates()
            ->getGeocentricEclipticalSphericalCoordinates($toi)
            ->getGeocentricEquatorialCoordinates($toi);

        $corrections = new GeocentricEquatorialCorrections($toi);
        $geoEqaCoordinates = $corrections->correctCoordinates($geoEqaCoordinates);

        $ra = $geoEqaCoordinates->getRightAscension();
        $d = $geoEqaCoordinates->getDeclination();

        var_dump(AngleUtil::dec2time($ra), AngleUtil::dec2angle($d));die();


        $geoEclSphCoordinates = $corrections->correctCoordinates($geoEclSphCoordinates);

//        $lon = $geoEclSphCoordinates->getLongitude();
//        $lat = $geoEclSphCoordinates->getLatitude();
        $ra = $geoEclSphCoordinates->getRightAscension();
        $de = $geoEclSphCoordinates->getDeclination();
        $radiusVector = $geoEclSphCoordinates->getRadiusVector();

        var_dump(AngleUtil::dec2time($ra), AngleUtil::dec2angle($de), $radiusVector);die();

        $this->assertEquals(26.11412, round($lon, 5));
        $this->assertEquals(-2.62060, round($lat, 5));
        $this->assertEquals(0.724602, round($radiusVector, 6));

        // TODO Very true final coordinates
        // ra 21h04m41.454s d=-18°53'16".84 distance = 0.91084596
    }

    public function XtestGetHeliocentricEclipticalRectangularCoordinates()
    {
        $toi = new TimeOfInterest(new \DateTime('1992-12-20 00:00:00'));
        $venus = new Venus($toi);

        $helEclRecCoordinates = $venus->getHeliocentricEclipticalRectangularCoordinates();

        $X = $helEclRecCoordinates->getX();
        $Y = $helEclRecCoordinates->getY();
        $Z = $helEclRecCoordinates->getZ();

        // TODO ...
//        $this->assertEquals(26.11412, round($X, 6));
//        $this->assertEquals(-2.62060, round($Y, 6));
//        $this->assertEquals(0.724602, round($Z, 6));
    }
}
