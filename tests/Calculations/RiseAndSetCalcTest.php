<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Sun;
use Andrmoel\AstronomyBundle\Calculations\EarthCalc;
use Andrmoel\AstronomyBundle\Calculations\RiseAndSetCalc;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;
use PHPUnit\Framework\TestCase;

class RiseAndSetCalcTest extends TestCase
{
    /**
     * @test
     * Meeus 15.a
     */
    public function test()
    {
        // Boston
        $location = new Location(42.3333, -71.0833);
        $toi = new TimeOfInterest(new \DateTime('1988-03-20 00:00:00'));

        // Berlin
//        $location = new Location(52.524, 13.411);
//        $toi = new TimeOfInterest(new \DateTime());

        $transit = RiseAndSetCalc::getTransit(Sun::class, $location, $toi);

        $this->assertEquals(94.9806, round($transit, 4));
    }
}
