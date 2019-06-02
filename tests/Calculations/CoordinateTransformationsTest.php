<?php

namespace Andrmoel\AstronomyBundle\Tests\Calculations;

use Andrmoel\AstronomyBundle\Calculations\CoordinateTransformations;
use PHPUnit\Framework\TestCase;

class CoordinateTransformationsTest extends TestCase
{
    /**
     * @test
     */
    public function rectangular2sphericalTest()
    {
        $x = 0.621746;
        $y = -0.66481;
        $z = -0.033134;

        $coord = CoordinateTransformations::rectangular2spherical($x, $y, $z);

        $this->assertEquals(313.082894, round($coord[0], 6));
        $this->assertEquals(-2.084721, round($coord[1], 6));
        $this->assertEquals(0.910845, round($coord[2], 6));
    }

    /**
     * @test
     */
    public function spherical2rectangularTest()
    {
        $longitude = 313.082894;
        $latitude = -2.084721;
        $radiusVector = 0.910845;

        $coord = CoordinateTransformations::spherical2rectangular(
            $longitude,
            $latitude,
            $radiusVector
        );

        $this->assertEquals(0.621746, round($coord[0], 6));
        $this->assertEquals(-0.66481, round($coord[1], 6));
        $this->assertEquals(-0.033134, round($coord[2], 6));
    }

    /**
     * @test
     */
    public function eclipticalSpherical2equatorialSphericalTest()
    {
        $T = -0.070321697467488; // 1992-12-20 00:00:00
        $longitude = 313.082894;
        $latitude = -2.084721;
        $radiusVector = 0.910845;

        $coord = CoordinateTransformations::eclipticalSpherical2equatorialSpherical(
            $longitude,
            $latitude,
            $radiusVector,
            $T
        );

        $this->assertEquals(316.174262, round($coord[0], 6));
        $this->assertEquals(-18.887468, round($coord[1], 6));
        $this->assertEquals(0.910845, round($coord[2], 6));
    }

    /**
     * @test
     */
    public function equatorialSpherical2eclipticalSphericalTest()
    {
        $T = -0.070321697467488; // 1992-12-20 00:00:00
        $rightAscension = 316.174262;
        $declination = -18.887468;
        $radiusVector = 0.910845;

        $coord = CoordinateTransformations::equatorialSpherical2eclipticalSpherical(
            $rightAscension,
            $declination,
            $radiusVector,
            $T
        );

        $this->assertEquals(313.082894, round($coord[0], 6));
        $this->assertEquals(-2.084721, round($coord[1], 6));
        $this->assertEquals(0.910845, round($coord[2], 6));
    }
}
