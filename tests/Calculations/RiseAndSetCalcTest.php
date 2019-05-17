<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Moon;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Venus;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Sun;
use Andrmoel\AstronomyBundle\Calculations\RiseSetCulminationCalculation;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
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
        $location = new Location(52.524, 13.411);
        $toi = new TimeOfInterest();

        $ras = new RiseSetCulminationCalculation(Sun::class, $location, $toi);

        var_dump("----");
        var_dump($ras->getRise()->getDateTime()->format('Y-m-d H:i:s'));
        var_dump($ras->getTransit()->getDateTime()->format('Y-m-d H:i:s'));
        var_dump($ras->getSet()->getDateTime()->format('Y-m-d H:i:s'));
    }
}
