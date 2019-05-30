<?php

namespace Andrmoel\AstronomyBundle\Tests;

use Andrmoel\AstronomyBundle\CalculationCache;
use PHPUnit\Framework\TestCase;

class CalculationCacheTest extends TestCase
{
    /**
     * @test
     */
    public function hasTest()
    {
        $cacheKey = 'foo';
        $time = 0.1234;
        $value = ['bar', 123];

        CalculationCache::clear();

        $this->assertFalse(CalculationCache::has($cacheKey, $time));

        CalculationCache::set($cacheKey, $time, $value);

        $this->assertTrue(CalculationCache::has($cacheKey, $time));
    }

    /**
     * @test
     */
    public function getTest()
    {
        $cacheKey = 'foo';
        $time = 0.1234;
        $value = ['bar', 123];

        CalculationCache::clear();

        $this->assertNull(CalculationCache::get($cacheKey, $time));

        CalculationCache::set($cacheKey, $time, $value);

        $this->assertEquals($value, CalculationCache::get($cacheKey, $time));
    }
}
