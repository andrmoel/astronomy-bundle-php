<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects;

use Andrmoel\AstronomyBundle\Coordinates\EclipticalCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\EquatorialCoordinates;
use Andrmoel\AstronomyBundle\Coordinates\LocalHorizontalCoordinates;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Util;

class Moon extends AstronomicalObject
{
    private $argumentsLR = array(
        [0, 0, 1, 0, 6288774, -20905355],
        [2, 0, -1, 0, 1274027, -3699111],
        [2, 0, 0, 0, 658314, -2955968],
        [0, 0, 2, 0, 213618, -569925],
        [0, 1, 0, 0, -185116, 48888],
        [0, 0, 0, 2, -114332, -3149],
        [2, 0, -2, 0, 58793, 246158],
        [2, -1, -1, 0, 57066, -152138],
        [2, 0, 1, 0, 53322, -170733],
        [2, -1, 0, 0, 45758, -204586],
        [0, 1, -1, 0, -40923, -129620],
        [1, 0, 0, 0, -34720, 108743],
        [0, 1, 1, 0, -30383, 104755],
        [2, 0, 0, -2, 15327, 10321],
        [0, 0, 1, 2, -12528, 0],
        [0, 0, 1, -2, 10980, 79661],
        [4, 0, -1, 0, 10675, -34782],
        [0, 0, 3, 0, 10034, -23210],
        [4, 0, -2, 0, 8548, -21636],
        [2, 1, -1, 0, -7888, 24208],
        [2, 1, 0, 0, -6766, 30824],
        [1, 0, -1, 0, -5163, -8379],
        [1, 1, 0, 0, 4987, -16675],
        [2, -1, 1, 0, 4036, -12831],
        [2, 0, 2, 0, 3994, -10445],
        [4, 0, 0, 0, 3861, -11650],
        [2, 0, -3, 0, 3665, 14403],
        [0, 1, -2, 0, -2689, -7003],
        [2, 0, -1, 2, -2602, 0],
        [2, -1, -2, 0, 2390, 10056],
        [1, 0, 1, 0, -2348, 6322],
        [2, -2, 0, 0, 2236, -9884],
        [0, 1, 2, 0, -2120, 5751],
        [0, 2, 0, 0, -2069, 0],
        [2, -2, -1, 0, 2048, -4950],
        [2, 0, 1, -2, -1773, 4130],
        [2, 0, 0, 2, -1595, 0],
        [4, -1, -1, 0, 1215, -3958],
        [0, 0, 2, 2, -1110, 0],
        [3, 0, -1, 0, -892, 3258],
        [2, 1, 1, 0, -810, 2616],
        [4, -1, -2, 0, 759, -1897],
        [0, 2, -1, 0, -713, -2117],
        [2, 2, -1, 0, -700, 2354],
        [2, 1, -2, 0, 691, 0],
        [2, -1, 0, -2, 596, 0],
        [4, 0, 1, 0, 549, -1423],
        [0, 0, 4, 0, 537, -1117],
        [4, -1, 0, 0, 520, -1571],
        [1, 0, -2, 0, -487, -1739],
        [2, 1, 0, -2, -399, 0],
        [0, 0, 2, -2, -381, -4421],
        [1, 1, 1, 0, 351, 0],
        [3, 0, -2, 0, -340, 0],
        [4, 0, -3, 0, 330, 0],
        [2, -1, 2, 0, 327, 0],
        [0, 2, 1, 0, -323, 1165],
        [1, 1, -1, 0, 299, 0],
        [2, 0, 3, 0, 294, 0],
        [2, 0, -1, -2, 0, 8752],
    );


    private $argumentsB = array(
        [0, 0, 0, 1, 5128122],
        [0, 0, 1, 1, 280602],
        [0, 0, 1, -1, 277693],
        [2, 0, 0, -1, 173237],
        [2, 0, -1, 1, 55413],
        [2, 0, -1, -1, 46271],
        [2, 0, 0, 1, 32573],
        [0, 0, 2, 1, 17198],
        [2, 0, 1, -1, 9266],
        [0, 0, 2, -1, 8822],
        [2, -1, 0, -1, 8216],
        [2, 0, -2, -1, 4324],
        [2, 0, 1, 1, 4200],
        [2, 1, 0, -1, -3359],
        [2, -1, -1, 1, 2463],
        [2, -1, 0, 1, 2211],
        [2, -1, -1, -1, 2065],
        [0, 1, -1, -1, -1870],
        [4, 0, -1, -1, 1828],
        [0, 1, 0, 1, -1794],
        [0, 0, 0, 3, -1749],
        [0, 1, -1, 1, -1565],
        [1, 0, 0, 1, -1491],
        [0, 1, 1, 1, -1475],
        [0, 1, 1, -1, -1410],
        [0, 1, 0, -1, -1344],
        [1, 0, 0, -1, -1335],
        [0, 0, 3, 1, 1107],
        [4, 0, 0, -1, 1021],
        [4, 0, -1, 1, 833],
        [0, 0, 1, -3, 777],
        [4, 0, -2, 1, 671],
        [2, 0, 0, -3, 607],
        [2, 0, 2, -1, 596],
        [2, -1, 1, -1, 491],
        [2, 0, -2, 1, -451],
        [0, 0, 3, -1, 439],
        [2, 0, 2, 1, 422],
        [2, 0, -3, -1, 421],
        [2, 1, -1, 1, -366],
        [2, 1, 0, 1, -351],
        [4, 0, 0, 1, 331],
        [2, -1, 1, 1, 315],
        [2, -2, 0, -1, 302],
        [0, 0, 1, 3, -283],
        [2, 1, 1, -1, -229],
        [1, 1, 0, -1, 223],
        [1, 1, 0, 1, 223],
        [0, 1, -2, -1, -220],
        [2, 1, -1, -1, -220],
        [1, 0, 1, 1, -185],
        [2, -1, -2, -1, 181],
        [0, 1, 2, 1, -177],
        [4, 0, -2, -1, 176],
        [4, -1, -1, -1, 166],
        [1, 0, 1, -1, -164],
        [4, 0, 1, -1, 132],
        [1, 0, -1, -1, -119],
        [4, -1, 0, -1, 115],
        [2, -2, 0, 1, 107],
    );

    // Sum  parameter
    private $sumL = 0;
    private $sumR = 0;
    private $sumB = 0;


    public function __construct(TimeOfInterest $toi = null)
    {
        parent::__construct($toi);
        $this->initializeSumParameter();
    }


    public function setTimeOfInterest(TimeOfInterest $toi): void
    {
        parent::setTimeOfInterest($toi);
        $this->initializeSumParameter();
    }


    private function initializeSumParameter()
    {
        $T = $this->T;

        // Mean longitude
        $L = $this->getMeanLongitude();
        // Mean elongation of the moon
        $D = 297.8501921 + 445267.1114034 * $T - 0.0018819 * pow($T, 2) + pow($T, 3) / 545868 - pow($T, 4) / 113065000;
        $D = Util::normalizeAngle($D);
        // Mean anomaly of the sun
        $Msun = 357.5291092 + 35999.0502909 * $T - 0.0001536 * pow($T, 2) + pow($T, 3) / 24490000;
        $Msun = Util::normalizeAngle($Msun);
        // Mean anomaly of the moon
        $Mmoon = $this->getMeanAnomaly();
        // Argument of latitude
        $F = 93.2720950 + 483202.0175233 * $T - 0.0036539 * pow($T, 2) - pow($T, 3) / 352600 + pow($T, 4) / 86331000;
        $F = Util::normalizeAngle($F);

        // Action of venus
        $A1 = Util::normalizeAngle(119.75 + 131.849 * $T);
        // Action of jupiter
        $A2 = Util::normalizeAngle(53.09 + 479264.290 * $T);
        $A3 = Util::normalizeAngle(313.45 + 481266.484 * $T);
        $E = Util::normalizeAngle(1 - 0.002516 * $T - 0.0000074 * pow($T, 2));

        $sumL = 3958 * sin(deg2rad($A1))
            + 1962 * sin(deg2rad($L - $F))
            + 318 * sin(deg2rad($A2));

        $sumR = 0;

        $sumB = -2235 * sin(deg2rad($L))
            + 382 * sin(deg2rad($A3))
            + 175 * sin(deg2rad($A1 - $F))
            + 175 * sin(deg2rad($A1 + $F))
            + 127 * sin(deg2rad($L - $Mmoon))
            - 115 * sin(deg2rad($L + $Mmoon));

        foreach ($this->argumentsLR as $arg) {
            $argD = $arg[0];
            $argMsun = $arg[1];
            $argMmoon = $arg[2];
            $argF = $arg[3];
            $argSumL = $arg[4];
            $argSumR = $arg[5];

            $tmpSumL = sin(deg2rad($argD * $D + $argMsun * $Msun + $argMmoon * $Mmoon + $argF * $F));
            $tmpSumR = cos(deg2rad($argD * $D + $argMsun * $Msun + $argMmoon * $Mmoon + $argF * $F));

            switch ($argMsun) {
                case 1:
                case -1:
                    $tmpSumL = $tmpSumL * $argSumL * $E;
                    $tmpSumR = $tmpSumR * $argSumR * $E;
                    break;
                case 2:
                case -2:
                    $tmpSumL = $tmpSumL * $argSumL * $E * $E;
                    $tmpSumR = $tmpSumR * $argSumR * $E * $E;
                    break;
                default:
                    $tmpSumL = $tmpSumL * $argSumL;
                    $tmpSumR = $tmpSumR * $argSumR;
                    break;
            }

            $sumL += $tmpSumL;
            $sumR += $tmpSumR;
        }

        foreach ($this->argumentsB as $arg) {
            $argD = $arg[0];
            $argMsun = $arg[1];
            $argMmoon = $arg[2];
            $argF = $arg[3];
            $argSumB = $arg[4];

            $tmpSumB = sin(deg2rad($argD * $D + $argMsun * $Msun + $argMmoon * $Mmoon + $argF * $F));

            switch ($argMsun) {
                case 1:
                case -1:
                    $tmpSumB = $tmpSumB * $argSumB * $E;
                    break;
                case 2:
                case -2:
                    $tmpSumB = $tmpSumB * $argSumB * $E * $E;
                    break;
                default:
                    $tmpSumB = $tmpSumB * $argSumB;
                    break;
            }

            $sumB += $tmpSumB;
        }

        $this->sumL = $sumL;
        $this->sumR = $sumR;
        $this->sumB = $sumB;
    }


    /**
     * Meeus chapter 22
     * @return float
     */
    public function getMeanElongationFromSun(): float
    {
        $T = $this->T;

        $D = 297.85036 + 445267.111480 * $T - 0.0019142 * pow($T, 2) + pow($T, 3) / 189474;
        $D = Util::normalizeAngle($D);

        return $D;
    }


    /**
     * Meeus chapter 22
     * @return float
     */
    public function getMeanAnomaly(): float
    {
        $T = $this->T;

        // TODO besser, aber woher ist die Formel???
//        $Mmoon = 134.9633964 + 477198.8675055 * $T + 0.0087414 * pow($T, 2) + pow($T, 3) / 69699 - pow($T, 4) / 1471200;
        $Mmoon = 134.96298 + 477198.867398 * $T + 0.0086972 * pow($T, 2) + pow($T, 3) / 56250;
        $Mmoon = Util::normalizeAngle($Mmoon);

        return $Mmoon;
    }


    /**
     * Meeus chapter 22
     * @return float
     */
    public function getArgumentOfLatitude(): float
    {
        $T = $this->T;

        $F = 93.27191 + 483202.017538 * $T - 0.0036825 * pow($T, 2) + pow($T, 3) / 327270;
        $F = Util::normalizeAngle($F);

        return $F;
    }


    /**
     * Get distance to earth [km]
     * @return float
     */
    public function getDistanceToEarth(): float
    {
        $d = (385000.56 + ($this->sumR / 1000));

        return $d;
    }


    /**
     * Get latitude [°]
     * @return float
     */
    public function getLatitude(): float
    {
        $b = $this->sumB / 1000000;

        return $b;
    }


    public function getMeanLongitude(): float
    {
        $T = $this->T;

        $L = 218.3164477 + 481267.88123421 * $T - 0.0015786 * pow($T, 2) + pow($T, 3) / 538841 - pow($T, 4) / 65194000;
        $L = Util::normalizeAngle($L);

        return $L;
    }


    /**
     * Get longitude [°]
     * @return float
     */
    public function getLongitude(): float
    {
        $L = $this->getMeanLongitude();
        $l = $L + ($this->sumL / 1000000);

        return $l;
    }


    /**
     * Get apparent longitude
     * @return float
     */
    public function getApparentLongitude(): float
    {
        $l = $this->getLongitude();

        $earth = new Earth();
        $earth->setTimeOfInterest($this->toi);
        $phi = $earth->getNutation();

        $l = $l + $phi;

        return $l;
    }


    public function getEclipticalCoordinates(): EclipticalCoordinates
    {
        $lat = $this->getLatitude();
        $lon = $this->getLongitude();

        return new EclipticalCoordinates($lat, $lon);
    }


    public function getEquatorialCoordinates(): EquatorialCoordinates
    {
        $earth = new Earth($this->toi);
        $obliquityOfEcliptic = $earth->getObliquityOfEcliptic();

        return $this
            ->getEclipticalCoordinates()
            ->getEquatorialCoordinates($obliquityOfEcliptic);
    }


    public function getLocalHorizontalCoordinates(Location $location): LocalHorizontalCoordinates
    {
        return $this
            ->getEquatorialCoordinates()
            ->getLocalHorizontalCoordinates($location, $this->toi);
    }


    public function getEquatorialHorizontalParallax(): float
    {
        $d = $this->getDistanceToEarth();
        $pi = rad2deg(asin(6378.14 / $d));

        return $pi;
    }


    public function getIlluminatedFraction(): float
    {
        $equatorialCoordinates = $this->getEquatorialCoordinates();

        $aMoon = $equatorialCoordinates->getRightAscension();
        $dMoon = $equatorialCoordinates->getDeclination();
        $distMoon = $this->getDistanceToEarth();

        $sun = new Sun($this->toi);
        $equatorialCoordinates = $sun->getEquatorialCoordinates();
        $aSun = $equatorialCoordinates->getRightAscension();
        $dSun = $equatorialCoordinates->getDeclination();
        $distSun = $sun->getDistanceToEarth();

        $aMoon = deg2rad($aMoon);
        $aSun = deg2rad($aSun);
        $dMoon = deg2rad($dMoon);
        $dSun = deg2rad($dSun);

        $phi = acos(sin($dSun) * sin($dMoon) + cos($dSun) * cos($dMoon) * cos($aSun - $aMoon));
        $i = atan(($distSun * sin($phi)) / ($distMoon - $distSun * cos($phi)));

        // i must be between 0° and 180°
        $i = rad2deg($i);
        $i = Util::normalizeAngle($i, 180);
        $i = deg2rad($i);

        $k = (1 + cos($i)) / 2;

        return $k;
    }


    public function isWaxingMoon(): bool
    {
        $moonPhase1 = $this->getIlluminatedFraction();

        $toi = $this->getTimeOfInterest();
        $second = $toi->getSecond() + 1;
        $toi->setTime($toi->getYear(), $toi->getMonth(), $toi->getDay(), $toi->getHour(), $toi->getMinute(), $second);

        $moon = new self($toi);
        $moonPhase2 = $moon->getIlluminatedFraction();

        return $moonPhase2 > $moonPhase1;
    }


    /**
     * Get position angle of moon's bright limb
     * TODO evaluate (MEEUS 346)
     * @return float
     */
    public function getPositionAngleOfMoonsBrightLimb(): float
    {
        $earth = new Earth($this->toi);
        $obliquityOfEcliptic = $earth->getObliquityOfEcliptic();
        $equatorialCoordinates = $this->getEquatorialCoordinates($obliquityOfEcliptic);

        $aMoon = $equatorialCoordinates->getRightAscension();
        $dMoon = $equatorialCoordinates->getDeclination();

        $sun = new Sun();
        $sun->setTimeOfInterest($this->toi);
        $equatorialCoordinates = $sun->getEquatorialCoordinates();
        $aSun = $equatorialCoordinates->getRightAscension();
        $dSun = $equatorialCoordinates->getDeclination();

        $numerator = cos($dSun) * sin($aSun - $aMoon);
        $denominator = sin($dSun) * cos($dMoon) - cos($dSun) * sin($dMoon) * cos($aSun - $aMoon);

        $x = rad2deg(atan($numerator / $denominator));
        $x = Util::normalizeAngle($x);

        return $x;
    }
}
