<?php

require __DIR__ . '/../vendor/autoload.php';

use Andrmoel\AstronomyBundle\Calculations\VSOP87Calc;

$T = 0.012970568104; // TODO Test...
$res = VSOP87Calc::getVSOP87Result
(VSOP87Calc::VSOP87_PLANET_VENUS_HELIOCENTRIC_RECTANGULAR,
    VSOP87Calc::VSOP87_COEFFICIENT_A,
    $T
);

var_dump($res, -0.604958132783);
