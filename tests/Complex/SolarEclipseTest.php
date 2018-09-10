<?php

namespace Andrmoel\AstronomyBundle\Tests\Complex;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Earth;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Moon;
use Andrmoel\AstronomyBundle\Eclipses\BesselianElements;
use Andrmoel\AstronomyBundle\Eclipses\SolarEclipse;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use PHPUnit\Framework\TestCase;

class SolarEclipseTest extends TestCase
{
    public function testMoonRise()
    {
        $besselianElementsArray = array (
            'tMax' => '2457987.26900',
            't0' => 18,
            'dT' => 70.299999999999997,
            'x' =>
                array (
                    0 => -0.12957099999999999,
                    1 => 0.54064259999999997,
                    2 => -2.94E-5,
                    3 => -8.1000000000000004E-6,
                ),
            'y' =>
                array (
                    0 => 0.48541600000000001,
                    1 => -0.14163999999999999,
                    2 => -9.0500000000000004E-5,
                    3 => 1.9999999999999999E-6,
                ),
            'd' =>
                array (
                    0 => 11.866959599999999,
                    1 => -0.013622,
                    2 => -1.9999999999999999E-6,
                    3 => 0,
                ),
            'l1' =>
                array (
                    0 => 0.54209300000000005,
                    1 => 0.00012410000000000001,
                    2 => -1.1800000000000001E-5,
                    3 => 0,
                ),
            'l2' =>
                array (
                    0 => -0.0040249999999999999,
                    1 => 0.00012339999999999999,
                    2 => -1.17E-5,
                    3 => 0,
                ),
            'mu' =>
                array (
                    0 => 89.245429999999999,
                    1 => 15.00394,
                    2 => 0,
                    3 => 0,
                ),
            'tanF1' => 0.0046221999999999999,
            'tanF2' => 0.0045992000000000003,
        );

        $besselianElements = new BesselianElements($besselianElementsArray);

        // Madras, Oregon
        $lat = 44.630556;
        $lon = -121.129167;
        $location = new Location($lat, $lon);

        // Time of interest is the 21th of August 2017
        $toi = new TimeOfInterest();
        $toi->setTime(2017, 8, 21);

        $solarEclipse = new SolarEclipse($besselianElements);
        $solarEclipse->setLocation($location);

//        $c1 = $solarEclipse->getCircumstancesC1();

//        var_dump($c1);die();

echo <<<END

    Location: {$lat}°, {$lon}°
    Type: {$solarEclipse->getEclipseType()}
    Duration whole eclipse: {$solarEclipse->getEclipseDuration()} seconds
    Duration totality: {$solarEclipse->getEclipseUmbraDuration()} seconds
    Coverage: {$solarEclipse->getCoverage()}
    
    C1
END;
    }
}
