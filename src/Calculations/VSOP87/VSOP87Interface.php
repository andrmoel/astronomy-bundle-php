<?php

namespace Andrmoel\AstronomyBundle\Calculations\VSOP87;

interface VSOP87Interface
{
    public static function calculateA0($t): float;

    public static function calculateA1($t): float;

    public static function calculateA2($t): float;

    public static function calculateA3($t): float;

    public static function calculateA4($t): float;

    public static function calculateB0($t): float;

    public static function calculateB1($t): float;

    public static function calculateB2($t): float;

    public static function calculateB3($t): float;

    public static function calculateB4($t): float;

    public static function calculateC0($t): float;

    public static function calculateC1($t): float;

    public static function calculateC2($t): float;

    public static function calculateC3($t): float;

    public static function calculateC4($t): float;
}
