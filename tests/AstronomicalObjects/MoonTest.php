<?php

namespace Andrmoel\AstronomyBundle\Tests\AstronomicalObjects;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Moon;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use PHPUnit\Framework\TestCase;

class MoonTest extends TestCase
{
    /**
     * @test
     * Meeus 47.a
     */
    public function getGeocentricEclipticalSphericalCoordinatesTest()
    {
        $toi = TimeOfInterest::createFromString('1992-04-12 00:00:00');

        $moon = new Moon($toi);
        $geoEclSphCoordinates = $moon->getGeocentricEclipticalSphericalCoordinates();

        $longitude = $geoEclSphCoordinates->getLongitude();
        $latitude = $geoEclSphCoordinates->getLatitude();
        $radiusVector = $geoEclSphCoordinates->getRadiusVector();

        $this->assertEquals(133.167265, round($longitude, 6));
        $this->assertEquals(-3.229126, round($latitude, 6));
        $this->assertEquals(0.002463, round($radiusVector, 6));
    }

    /**
     * @test
     */
    public function getGeocentricEquatorialRectangularCoordinatesTest()
    {
        $toi = TimeOfInterest::createFromString('1992-04-12 00:00:00');

        $moon = new Moon($toi);
        $geoEquRecCoords = $moon->getGeocentricEquatorialRectangularCoordinates();

        $X = $geoEquRecCoords->getX();
        $Y = $geoEquRecCoords->getY();
        $Z = $geoEquRecCoords->getZ();

        $this->assertEquals(-0.001682, round($X, 6));
        $this->assertEquals(0.001701, round($Y, 6));
        $this->assertEquals(0.0005860, round($Z, 6));
    }

    /**
     * @test
     * Meeus 47.a
     */
    public function getGeocentricEquatorialSphericalCoordinatesTest()
    {
        $toi = TimeOfInterest::createFromString('1992-04-12 00:00:00');

        $moon = new Moon($toi);
        $geoEquSphCoordinates = $moon->getGeocentricEquatorialSphericalCoordinates();

        $rightAscension = $geoEquSphCoordinates->getRightAscension();
        $declination = $geoEquSphCoordinates->getDeclination();

        $this->assertEquals(134.688470, round($rightAscension, 5));
        $this->assertEquals(13.76837, round($declination, 5));
    }

    /**
     * @test
     * Meeus 47.a
     */
    public function getLocalHorizontalCoordinatesTest()
    {
        $toi = TimeOfInterest::createFromString('1992-04-12 00:00:00');

        // Berlin
        $location = Location::create(52.524, 13.411);

        $moon = new Moon($toi);
        $localHorizontalCoordinates = $moon->getLocalHorizontalCoordinates($location, false);

        $azimuth = $localHorizontalCoordinates->getAzimuth();
        $altitude = $localHorizontalCoordinates->getAltitude();

        $this->assertEquals(269.99761, round($azimuth, 5));
        $this->assertEquals(17.45298, round($altitude, 5));
    }

    /**
     * @test
     * Meeus 48.a
     */
    public function getIlluminatedFractionTest()
    {
        $toi = TimeOfInterest::createFromString('1992-04-12 00:00:00');

        $moon = new Moon($toi);
        $illuminatedFraction = $moon->getIlluminatedFraction();

        $this->assertEquals(0.68, round($illuminatedFraction, 2));
    }

    /**
     * @test
     */
    public function isWaxingMoonTest()
    {
        $toi = TimeOfInterest::createFromString('2018-09-17 00:00:00');

        $moon = new Moon($toi);
        $isWaxingMoon = $moon->isWaxingMoon();

        $this->assertTrue($isWaxingMoon);

        $toi = TimeOfInterest::createFromString('2018-10-02 00:00:00');

        $moon = new Moon($toi);
        $isWaxingMoon = $moon->isWaxingMoon();

        $this->assertFalse($isWaxingMoon);
    }

    /**
     * @test
     * Meeus 48.a
     */
    public function getPositionAngleOfMoonsBrightLimbTest()
    {
        $toi = TimeOfInterest::createFromString('1992-04-12 00:00:00');

        $moon = new Moon($toi);
        $x = $moon->getPositionAngleOfMoonsBrightLimb();

        $this->assertEquals(285.0, round($x, 1));
    }
}
