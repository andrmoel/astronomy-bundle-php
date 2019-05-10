<?php

namespace Andrmoel\AstronomyBundle\Tests\AstronomicalObgetGeocentricEclipticalSphericalCoordinatesjects;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Sun;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use PHPUnit\Framework\TestCase;

class SunTest extends TestCase
{
    /**
     * Meeus 25.b
     */
    public function testGetGeoCentricEclipticalSphericalCoordinates()
    {
        $toi = new TimeOfInterest(new \DateTime('1992-10-13 00:00:00'));

        $sun = new Sun($toi);
        $geoEclSphCoordinates = $sun->getGeoCentricEclipticalSphericalCoordinates();

        $lon = $geoEclSphCoordinates->getLongitude();
        $lat = $geoEclSphCoordinates->getLatitude();

        $this->assertEquals(199.9073, round($lon, 5));
        $this->assertEquals(0.00021, round($lat, 5));

        /*
         * TODO ...
         *
         * Correct coordinates
         * Theta = 199°54'26".18
         * lon = 199°54'21".56
         * lat = 0°0°0".72
         * R = 0.99760853
         *
         * appRa = 13h13m30s.749
         * appD = -7°47'01".74
         */
    }

    /**
     * @test
     * Meeus 25.a
     */
    public function getGeocentricEquatorialCoordinatesTest()
    {
        $toi = new TimeOfInterest(new \DateTime('1992-10-13 00:00:00'));

        $sun = new Sun($toi);
        $geoEquCoordinates = $sun->getGeocentricEquatorialCoordinates();

        $rightAscension = $geoEquCoordinates->getRightAscension();
        $declination = $geoEquCoordinates->getDeclination();
        $radiusVector = $geoEquCoordinates->getRadiusVector();

        $this->assertEquals(198.38082, round($rightAscension, 5));
        $this->assertEquals(-7.78507, round($declination, 5));
        $this->assertEquals(0.99766, round($radiusVector, 5));
    }

    // TODO ...
    public function XtestGetRectangularGeocentricEquatorialCoordinates()
    {
        $toi = new TimeOfInterest(new \DateTime('1992-10-13 00:00:00'));

        $sun = new Sun($toi);
        $equatorialCoordinates = $sun->getRectangularGeocentricEquatorialCoordinates();
    }

    /**
     * @test
     * @throws \Exception
     */
    public function getTwilightTest()
    {
        $data = array(
            ['2018-09-18 12:00:00', Sun::TWILIGHT_DAY],
            ['2018-09-18 17:30:00', Sun::TWILIGHT_CIVIL],
            ['2018-09-18 18:00:00', Sun::TWILIGHT_NAUTICAL],
            ['2018-09-18 18:30:00', Sun::TWILIGHT_ASTRONOMICAL],
            ['2018-09-18 19:30:00', Sun::TWILIGHT_NIGHT],
        );

        $lat = 52.518611;
        $lon = 13.408333;
        $location = new Location($lat, $lon);

        foreach ($data as $t) {
            $toi = new TimeOfInterest(new \DateTime($t[0]));

            $sun = new Sun($toi);
            $this->assertEquals($t[1], $sun->getTwilight($location));
        }
    }
}
