<?php

namespace Andrmoel\AstronomyBundle\Tests;

use Andrmoel\AstronomyBundle\Corrections;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use PHPUnit\Framework\TestCase;

class CorrectionsTest extends TestCase
{
    public function testCorrectEffectOfNutation()
    {
        $toi = new TimeOfInterest(new \DateTime('2028-11-13 04:33:00'));
        $corrections = new Corrections($toi);

        $rightAscension = 41.5472;
        $declination = 49.3485;

        $corrections->correctEffectOfNutation($rightAscension, $declination);
    }
}
