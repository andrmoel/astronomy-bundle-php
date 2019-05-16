<?php

namespace Andrmoel\AstronomyBundle\Tests\Corrections;

use Andrmoel\AstronomyBundle\Coordinates\LocalHorizontalCoordinates;
use Andrmoel\AstronomyBundle\Corrections\LocalHorizontalCorrections;
use PHPUnit\Framework\TestCase;

class LocalHorizontalCorrectionsTest extends TestCase
{
//    public function testCorrectAtmosphericRefraction()
//    {
//        $testData = [
//            [-1.5, -0.78],
//            [-1, -0.35],
//            [-0.5, 0.06],
//            [0, 0.48],
//            [5, 5.16],
//            [10, 10.09],
//            [20, 20.05],
//            [30, 30.03],
//            [40, 40.02],
//            [50, 50.01],
//            [60, 60.01],
//            [70, 70.01],
//            [80, 80.0],
//            [90, 90.0],
//        ];
//
//        $corrections = new LocalHorizontalCorrections();
//
//        foreach ($testData as $data) {
//            $locHorCoordinates = new LocalHorizontalCoordinates(0, $data[0]);
//            $locHorCoordinates = $corrections->correctAtmosphericRefraction($locHorCoordinates);
//
//            $altitude = $locHorCoordinates->getAltitude();
//
//            $this->assertEquals($data[1], round($altitude, 2));
//        }
//    }
}
