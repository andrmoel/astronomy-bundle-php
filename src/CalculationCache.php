<?php

namespace Andrmoel\AstronomyBundle;

class CalculationCache
{
    private static $cache = [];

    public static function set(string $cacheKey, float $time, $value): void
    {
        $key = self::getCacheKey($cacheKey, $time);

        self::$cache[$key] = $value;
    }

    public static function has(string $cacheKey, float $time): bool
    {
        $key = self::getCacheKey($cacheKey, $time);

        return isset(self::$cache[$key]);
    }

    public static function get(string $cacheKey, float $time)
    {
        if (self::has($cacheKey, $time)) {
            $key = self::getCacheKey($cacheKey, $time);

            return self::$cache[$key];
        }

        return null;
    }

    private static function getCacheKey(string $cacheKey, float $time): string
    {
        return $cacheKey . (string) $time;
    }
}
