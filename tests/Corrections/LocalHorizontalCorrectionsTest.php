<?php

namespace Andrmoel\AstronomyBundle\Tests\Corrections;

use Andrmoel\AstronomyBundle\Coordinates\LocalHorizontalCoordinates;
use Andrmoel\AstronomyBundle\Corrections\LocalHorizontalCorrections;
use PHPUnit\Framework\TestCase;

class LocalHorizontalCorrectionsTest extends TestCase
{
    /**
     * @test
     */
    public function correctAtmosphericRefractionTest()
    {
        $array = [
            [0, -20, -20.04501],
            [0, -10, -10.07926],
            [0, -5, -4.9996],
            [0, -2, -1.25767],
            [0, -1.5, -0.78033],
            [0, -1, -0.35342],
            [0, -0.5, 0.06146],
            [0, 0, 0.48303],
            [0, 0.5, 0.91673],
            [0, 1, 1.3624],
            [0, 1.5, 1.81819],
            [0, 2, 2.2821],
            [0, 2.5, 2.75238],
            [0, 3, 3.22769],
            [0, 3.5, 3.70694],
            [0, 4, 4.18934],
            [0, 4.5, 4.67426],
            [0, 5, 5.16124],
            [0, 10, 10.09013],
            [0, 20, 20.04569],
        ];

        foreach ($array as $data) {
            $locHorCoord = new LocalHorizontalCoordinates($data[0], $data[1]);

            $locHorCoord = LocalHorizontalCorrections::correctAtmosphericRefraction($locHorCoord);

            $azimuth = $locHorCoord->getAzimuth();
            $altitude = $locHorCoord->getAltitude();

            $this->assertEquals(0, $azimuth);
            $this->assertEquals($data[2], round($altitude, 5));
        }
    }
}
