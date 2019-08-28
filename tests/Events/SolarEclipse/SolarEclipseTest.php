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
            [Location::create(-27.92463, 98.13337), 0.0], // Eclipse is not visible
            [Location::create(-26.31658, 111.23312), 2128.3], // Sunrise between MAX and C4
            [Location::create(-24.57520, 118.68513), 4289.8], // Sunrise between MAX and C3
            [Location::create(-24.50490, 119.34813), 4493.1], // Sunrise between C2 and MAX
            [Location::create(-23.39042, 122.51219), 5493.0], // Sunrise between C1 and C2
            [Location::create(-17.79923, 136.94227), 9763.8], // Whole eclipse is visible
            [Location::create(-2.02670, -137.90145), 8106.0], // Sunset between C3 and C4
            [Location::create(-5.35914, -127.31062), 4569.6], // Sunset between C3 and MAX
            [Location::create(-5.59057, -126.56625), 4332.8], // Sunset between C2 and MAX
            [Location::create(-8.26987, -117.42271), 1664.4], // Sunset between C1 and MAX
        ];

        foreach ($array as $data) {
            $solarEclipse->setLocation($data[0]);
            $duration = $solarEclipse->getEclipseDuration();

            $this->assertEquals($data[1], round($duration, 1));
        }
    }

    /**
     * @test
     */
    public function getEclipseUmbraDurationTest()
    {
        $toi = TimeOfInterest::createFromString('2013-05-10');
        $solarEclipse = SolarEclipse::create($toi);

        $array = [
            [Location::create(-32.96072, 90.95353), 0.0], // Eclipse is not visible
            [Location::create(-24.85881, 117.13999), 0.0], // Eclipse happens right before sunset
            [Location::create(-24.53202, 118.92287),118.3], // Eclipse happens during sunrise
            [Location::create(-24.20177, 120.08742), 251.3], // Eclipse happens right after sunrise
            [Location::create(-15.42228, 141.88698), 279.6], // Eclipse happens high above horizon
            [Location::create(-5.10543, -128.12308), 255.5], // Eclipse happens right before sunset
            [Location::create(-5.48116, -126.97582), 156.8], // Eclipse happens during sunset
            [Location::create(-5.82896, -125.64058), 0.0], // Eclipse happens right after sunset
            [Location::create(-18.48712, 138.24995), 0.0], // No annular eclipse, only partial eclipse
        ];

        foreach ($array as $data) {
            $solarEclipse->setLocation($data[0]);
            $duration = $solarEclipse->getEclipseUmbraDuration();

            $this->assertEquals($data[1], round($duration, 1));
        }
    }
}
