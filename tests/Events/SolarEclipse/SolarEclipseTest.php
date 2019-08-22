<?php

namespace Andrmoel\AstronomyBundle\Tests\Eclipses;

use Andrmoel\AstronomyBundle\Events\SolarEclipse\SolarEclipse;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use PHPUnit\Framework\TestCase;

class SolarEclipseTest extends TestCase
{
    /**
     * @test
     */
    public function getEclipseDurationTest()
    {
        $toi = TimeOfInterest::createFromString('2013-05-10');
        $solarEclipse = SolarEclipse::create($toi);

        $array = [
//            [new Location(-27.92463, 98.13337), 0.0], // Eclipse is not visible
//            [new Location(-26.31658, 111.23312), 2128.3], // Sunrise between MAX and C4
//            [new Location(-24.57520, 118.68513), 4289.8], // Sunrise between MAX and C3
//            [new Location(-24.50490, 119.34813), 4493.1], // Sunrise between C2 and MAX
//            [new Location(-23.39042, 122.51219), 5493.0], // Sunrise between C1 and C2
            [new Location(-17.79923, 136.94227), 9763.8], // Whole eclipse is visible
            [new Location(-2.02670, -137.90145), 8106.0], // Sunset between C3 and C4
            [new Location(-5.35914, -127.31062), 4569.6], // Sunset between C3 and MAX
            [new Location(-5.59057, -126.56625), 4332.8], // Sunset between C2 and MAX
            [new Location(-8.26987, -117.42271), 1664.4], // Sunset between C1 and MAX
        ];

        foreach ($array as $data) {
            $solarEclipse->setLocation($data[0]);
            $duration = $solarEclipse->getEclipseDuration();

            $this->assertEquals($data[1], round($duration, 1));
        }

        // Edge case: Eclipse happens
    }
//
//    /**
//     * @test
//     */
//    public function getEclipseUmbraDurationTest()
//    {
//        $toi = TimeOfInterest::createFromString('2013-05-10');
//        $solarEclipse = SolarEclipse::create($toi);
//
//        $array = [
//            [new Location(-24.78330, 118.00307), 0.0], // Eclipse happens below horizon
////            [new Location(-24.53202, 118.92287), 120.0], // Eclipse happens during sunrise
//            [new Location(-24.20177, 120.08742), 251.3], // Eclipse happens after sunrise
////            [new Location(-24.78330, 118.00307), 0.0], // Eclipse happens above sunset
//        ];
//
//        foreach ($array as $data) {
//            $solarEclipse->setLocation($data[0]);
//            $duration = $solarEclipse->getEclipseUmbraDuration();
//
//            $this->assertEquals($data[1], round($duration, 1));
//        }
//    }
}
