<?php

namespace Andrmoel\AstronomyBundle\Tests\Events\RiseSetTransit;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Sun;
use Andrmoel\AstronomyBundle\Events\RiseSetTransit\RiseSetTransit;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use PHPUnit\Framework\TestCase;

class RiseSetTransitTest extends TestCase
{
    /**
     * Meeus 15.a
     * TODO Write test
     */
    public function test()
    {
        // Boston
        $location = new Location(42.3333, -71.0833);

        $toi = TimeOfInterest::createFromString('1988-03-20 00:00:00');
    }
}
