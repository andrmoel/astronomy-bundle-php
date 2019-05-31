<?php

include __DIR__ . '/../vendor/autoload.php';

use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

date_default_timezone_set('UTC');

$toi = TimeOfInterest::createFromString('1992-12-20 00:00:00');

$JD = $toi->getJulianDay();
$T = $toi->getJulianCenturiesFromJ2000();
$t = $toi->getJulianMillenniaFromJ2000();
$GMST = $toi->getGreenwichMeanSiderealTime();
$GMST = AngleUtil::dec2time($GMST);
$GAST = $toi->getGreenwichApparentSiderealTime();
$GAST = AngleUtil::dec2time($GAST);
$E = $toi->getEquationOfTime();
$E = AngleUtil::dec2time($E);

echo <<<END
+------------------------------------
| Time calculations
+------------------------------------
Date: {$toi} UTC

Julian Day: {$JD}
Julian Centuries J2000: {$T}
Julian Millennia J2000: {$t}
Greenwich Mean Siderial Time: {$GMST}
Greenwich Apparent Siderial Time: {$GAST}
Equation of Time: {$E}

END;
