<?php

namespace Andrmoel\AstronomyBundle\Corrections;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Earth;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Sun;
use Andrmoel\AstronomyBundle\Constants;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialCoordinates;
use Andrmoel\AstronomyBundle\TimeOfInterest;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

class GeocentricEquatorialCorrections
{
    private $toi;

    private $earth;
    private $sun;

    public function __construct(TimeOfInterest $toi)
    {
        $this->toi = $toi;

        $this->earth = new Earth($this->toi);
        $this->sun = new Sun($this->toi);
    }

    public function correctCoordinates(
        GeocentricEquatorialCoordinates $geoEquCoordinates
    ): GeocentricEquatorialCoordinates
    {
        // TODO When correcting precession, we get false values compared to stellarium
//        $geoEquCoordinates = $this->correctEffectOfPrecession($geoEquCoordinates);
        $geoEquCoordinates = $this->correctEffectOfNutation($geoEquCoordinates);
        $geoEquCoordinates = $this->correctEffectOfAberration($geoEquCoordinates);

        return $geoEquCoordinates;
    }

    public function correctEffectOfPrecession(
        GeocentricEquatorialCoordinates $geoEquCoordinates
    ): GeocentricEquatorialCoordinates
    {
        $T = $this->toi->getJulianCenturiesFromJ2000();

        $rightAscension = $geoEquCoordinates->getRightAscension();
        $declination = $geoEquCoordinates->getDeclination();
        $radiusVector = $geoEquCoordinates->getRadiusVector();

        // Meeus 21.2
        $zeta = AngleUtil::angle2dec('0°0\'2306.2181"') * $T
            + AngleUtil::angle2dec('0°0\'0.30188"') * pow($T, 2)
            + AngleUtil::angle2dec('0°0\'0.017988"') * pow($T, 3);
        $z = AngleUtil::angle2dec('0°0\'2306.2181"') * $T
            + AngleUtil::angle2dec('0°0\'1.09468"') * pow($T, 2)
            + AngleUtil::angle2dec('0°0\'0.018203"') * pow($T, 3);;
        $theta = AngleUtil::angle2dec('0°0\'2004.3109"') * $T
            - AngleUtil::angle2dec('0°0\'0.42665"') * pow($T, 2)
            - AngleUtil::angle2dec('0°0\'0.041833"') * pow($T, 3);

        $raRad = deg2rad($rightAscension);
        $dRad = deg2rad($declination);
        $zetaRad = deg2rad($zeta);
        $thetaRad = deg2rad($theta);

        // Meeus 21.3
        $A = cos($dRad) * sin($raRad + $zetaRad);
        $B = cos($thetaRad) * cos($dRad) * cos($raRad + $zetaRad) - sin($thetaRad) * sin($dRad);
        $C = sin($thetaRad) * cos($dRad) * cos($raRad + $zetaRad) + cos($thetaRad) * sin($dRad);

        $rightAscension = rad2deg(atan($A / $B)) + $z;
        $declination = rad2deg(asin($C));

        return new GeocentricEquatorialCoordinates($rightAscension, $declination, $radiusVector);
    }

    public function correctEffectOfNutation(
        GeocentricEquatorialCoordinates $geoEquCoordinates
    ): GeocentricEquatorialCoordinates
    {
        $rightAscension = $geoEquCoordinates->getRightAscension();
        $declination = $geoEquCoordinates->getDeclination();
        $radiusVector = $geoEquCoordinates->getRadiusVector();

        $dEps = $this->earth->getNutationInObliquity();
        $eps = $this->earth->getObliquityOfEcliptic();
        $dPhi = $this->earth->getNutationInLongitude();

        $raRad = deg2rad($rightAscension);
        $dRad = deg2rad($declination);
        $epsRad = deg2rad($eps);

        // Meeus 23.1
        $dRa1 = (cos($epsRad) + sin($epsRad) * sin($raRad) * tan($dRad)) * $dPhi - (cos($raRad) * tan($dRad)) * $dEps;
        $dD1 = (sin($epsRad) * cos($raRad)) * $dPhi + (sin($raRad)) * $dEps;

        $rightAscension += $dRa1;
        $declination += $dD1;

        return new GeocentricEquatorialCoordinates($rightAscension, $declination, $radiusVector);
    }

    /**
     * Expansion of annual aberration into trigonometric series
     * Ron, C. & Vondrak, J.
     * http://adsbit.harvard.edu//full/1986BAICz..37...96R/0000099.000.html
     *
     * @param GeocentricEquatorialCoordinates $geoEquCoordinates
     * @return GeocentricEquatorialCoordinates
     */
    public function correctEffectOfAberration(
        GeocentricEquatorialCoordinates $geoEquCoordinates
    ): GeocentricEquatorialCoordinates
    {
        $T = $this->toi->getJulianCenturiesFromJ2000();
        $rightAscension = $geoEquCoordinates->getRightAscension();
        $declination = $geoEquCoordinates->getDeclination();
        $radiusVector = $geoEquCoordinates->getRadiusVector();
        $c = Constants::LIGHT_SPEED_AU;

        $raRad = deg2rad($rightAscension);
        $dRad = deg2rad($declination);

        $l1 = 4.4026088 + 2608.7903142 * $T;
        $l2 = 3.1761467 + 1021.3285546 * $T;
        $l3 = 1.7534703 + 628.3075849 * $T;
        $l4 = 6.2034809 + 334.0612431 * $T;
        $l5 = 0.5995465 + 52.9690965 * $T;
        $l6 = 0.8740168 + 21.3299095 * $T;
        $l7 = 5.4812939 + 7.4781599 * $T;
        $l8 = 5.3118863 * 3.8133036 * $T;
        $w = 3.8103444 + 8399.6847337 * $T;
        $D = 5.1984667 + 7771.3771486 * $T;
        $l = 2.3555559 + 8328.6914289 * $T;
        $ls = 6.2400601 + 628.3019553 * $T;
        $F = 1.6279052 + 8433.4661601 * $T;

        $X = 0.0;
        $Y = 0.0;
        $Z = 0.0;

        //Velocity components of the Earth-Moon barycenter heliocentric motion
        $components = array(
            [0, 0, 1, 0, 0, 0, -1719919 - 2 * $T, -25, 25 - 13 * $T - 1 * pow($T, 2), 1578094 + 156 * $T, 10 + 32 * $T + pow($T, 2), 684187 - 358 * $T],
            [0, 0, 2, 0, 0, 0, 6434 + 141 * $T, 28007 - 107 * $T * -1 * pow($T, 2), 25697 - 95 * $T - 1 * pow($T, 2), -5904 - 130 * $T, 11141 - 48 * $T, -2559 - 55 * $T],
            [0, 0, 3, 0, 0, 0, 486 - 5 * $T, -236 * -4 * $T, -216 - 4 * $T, -446 + 5 * $T, -94 - 2 * $T, -193 + 2 * $T],
            [0, 0, 2, 0, -1, 0, 31, 1, 1, -28, 0, -12],
            [0, 0, 3, -8, 3, 0, 8, -28, 25, 8, 11, 3],
            [0, 0, 5, -8, 3, 0, 8, -28, -25, -8, -11, -3],
            [0, 1, 0, 0, 0, 0, -25, 0, 0, 23, 0, 10],
            [0, 2, -1, 0, 0, 0, 21, 0, 0, -19, 0, -8],
            [0, 0, 1, 0, -2, 0, 16, 0, 0, 15, 1, 7],
            [0, 0, 1, 0, 1, 0, 11, -1, -1, -10, -1, -5],
            [0, 2, -2, 0, 0, 0, 0, -11, -10, 0, -4, 0],
            [0, 0, 1, 0, -1, 0, -11, -2, -2, 9, -1, 4],
            [0, 0, 4, 0, 0, 0, -7, -8, -8, 6, -3, 3],
            [0, 0, 3, 0, -2, 0, -10, 0, 0, 9, 0, 4],
            [0, 1, -2, 0, 0, 0, -9, 0, 0, -9, 0, -4],
            [0, 2, -3, 0, 0, 0, -9, 0, 0, -8, 0, -4],
            [0, 2, -3, 0, 0, 0, 0, -9, 8, 0, 3, 0],
            [0, 0, 3, -2, 0, 0, 8, 0, 0, -8, 0, -3],
            [0, 8, -12, 0, 0, 0, -4, -7, -6, 4, -3, 2],
            [0, 8, -14, 0, 0, 0, -4, -7, 6, -4, 3, -2],
            [0, 0, 0, 2, 0, 0, -6, -5, -4, 5, -2, 2],
            [0, 3, -4, 0, 0, 0, -1, -1, -2, -7, 1, -4],
            [0, 0, 2, 0, -2, 0, 4, -6, -5, -4, -2, -2],
            [0, 3, -3, 0, 0, 0, 0, -7, -6, 0, -3, 0],
            [0, 0, 2, -2, 0, 0, 5, -5, -4, -5, -2, -2],
            [0, 3, -6, 0, 0, 0, 4, -1, 1, 4, 0, 2],
            [0, 0, 0, 0, 1, 0, -4, 0, 0, 3, 0, 1],
            [0, 0, 9, -16, 4, 5, -1, -3, -3, 1, -1, 0],
            [0, 0, 7, -16, 4, 5, -1, -3, 3, -1, 1, 0],
            [0, 0, 1, 0, -3, 0, 3, 1, 0, 3, 0, 1],
            [0, 0, 2, 0, -3, 0, 3, -1, -1, 1, 0, 1],
            [0, 4, -5, 0, 0, 0, -2, 0, 0, -3, 0, -1],
            [0, 0, 1, -4, 0, 0, 1, -2, 2, 1, 1, 1],
            [0, 0, 3, 0, -3, 0, -2, -1, 0, 2, 0, 1],
            [0, 0, 3, -4, 0, 0, 1, -2, -2, -1, -1, 0],
            [0, 3, -2, 0, 0, 0, 2, 0, 0, -2, 0, -1],
            [0, 0, 4, -4, 0, 0, 2, -1, -1, -2, 0, -1],
            [0, 0, 2, 0, 0, -1, 2, 0, 0, -2, 0, -1],
            [0, 0, 3, -3, 0, 0, 2, -1, -1, -1, 0, -1],
            [0, 0, 3, 0, -1, 0, 0, -2, -1, 0, -1, 0],
            [0, 0, 1, 0, 0, 1, 0, -1, -1, 0, -1, 0],
            [0, 0, 0, 0, 2, 0, -1, -1, -1, 1, -1, 0],
            [0, 0, 2, -1, 0, 0, 1, 0, 0, -1, 0, -1],
            [0, 0, 1, 0, 0, -1, 0, -1, -1, 0, -1, 0],
            [0, 5, -6, 0, 0, 0, -2, 0, 0, -1, 0, 0],
            [0, 0, 1, -3, 0, 0, 1, -1, 1, 1, 0, 0],
            [0, 3, -6, 4, 0, 0, -1, 1, 1, 1, 0, 0],
            [0, 3, -8, 4, 0, 0, -1, 1, -1, -1, 0, 0],
            [0, 0, 4, -5, 0, 0, 1, -1, -1, 0, 0, 0],
            [0, 1, 1, 0, 0, 0, 0, 1, 1, 0, 0, 0],
            [0, 3, -5, 0, 0, 0, 0, -1, 1, 0, 0, 0],
            [0, 6, -7, 0, 0, 0, -1, 0, 0, -1, 0, 0],
            [0, 10, -9, 0, 0, 0, 1, 0, 0, -1, 0, 0],
            [0, 0, 2, -8, 3, 0, 1, 0, 0, 1, 0, 0],
            [0, 0, 6, -8, 3, 0, -1, 0, 0, 1, 0, 0],
            [0, 0, 1, -2, 0, 0, 1, 0, 0, 1, 0, 0],
            [0, 0, 9, -15, 0, 0, -1, 0, 0, 1, 0, 0],
            [0, 0, 1, 0, -2, 5, 1, 0, 0, -1, 0, 0],
            [0, 0, 1, 0, 2, -5, -1, 0, 0, 1, 0, 0],
            [0, 0, 1, 0, 0, -2, 1, 0, 0, 1, 0, 0],
            [0, 0, 0, 1, 0, 0, -1, 0, 0, 1, 0, 0],
            [0, 0, 7, -15, 0, 0, -1, 0, 0, -1, 0, 0],
            [0, 2, 0, 0, 0, 0, 0, -1, -1, 0, 0, 0],
            [0, 0, 2, 0, 2, -5, 0, 1, 1, 0, 0, 0],
            [2, 0, -2, 0, 0, 0, 0, 1, -1, 0, 0, 0],
            [0, 0, 9, -19, 0, 3, 0, 1, -1, 0, 0, 0],
            [0, 0, 11, -19, 0, 3, 0, 1, 1, 0, 0, 0],
            [0, 0, 2, -5, 0, 0, 0, -1, 1, 0, 0, 0],
            [0, 5, -9, 0, 0, 0, 0, 1, -1, 0, 0, 0],
            [0, 11, -10, 0, 0, 0, 1, 0, 0, 0, 0, 0],
            [0, 4, -4, 0, 0, 0, 0, 1, 0, 0, 0, 0],
            [0, 0, 2, 0, -4, 0, 1, 0, 0, 0, 0, 0],
            [0, 0, 5, -6, 0, 0, 0, -1, 0, 0, 0, 0],
            [0, 5, -5, 0, 0, 0, 0, 1, 0, 0, 0, 0],
            [0, 0, 4, 0, -3, 0, -1, 0, 0, 0, 0, 0],
            [0, 4, -6, 0, 0, 0, 0, -1, 0, 0, 0, 0],
            [0, 5, -7, 0, 0, 0, 0, 0, 1, 0, 0, 0],
            [0, 0, 4, 0, -2, 0, 0, 0, 1, 0, 0, 0],
            [0, 0, 3, 0, -4, 0, 0, 0, 0, 1, 0, 0],
            [0, 7, -8, 0, 0, 0, 0, 0, 0, -1, 0, 0]
        );

        foreach ($components as $component) {
            if (count($component) != 12) {
                var_dump($component);
                die();
            }
            $A = $l1 * $component[0]
                + $l2 * $component[1]
                + $l3 * $component[2]
                + $l4 * $component[3]
                + $l5 * $component[4]
                + $l6 * $component[5];
            $ARad = $A;

            $X += $component[6] * sin($ARad) + $component[7] * cos($ARad);
            $Y += $component[8] * sin($ARad) + $component[9] * cos($ARad);
            $Z += $component[10] * sin($ARad) + $component[11] * cos($ARad);
        }

        // Velocity components of the sun
        $componentsVelSun = array(
            [0, 0, 1, 0, 0, 0, 719, 0, 6, -660, -15, -283],
            [0, 0, 0, 1, 0, 0, 159, 0, 2, -147, -6, -61],
            [0, 0, 2, 0, 0, 0, 34, -9, -8, -31, -4, -13],
            [0, 0, 0, 0, 1, 0, 17, 0, 0, -16, 0, -7],
            [0, 0, 0, 0, 0, 1, 16, 0, 1, -15, -3, -6],
            [0, 0, 0, 2, 0, 0, 0, -9, -8, 0, -3, 1],
            [1, 0, 0, 0, 0, 0, 6, 0, 0, -6, 0, -2],
            [0, 1, 0, 0, 0, 0, 5, 0, 0, -5, 0, -2],
            [0, 0, 3, 0, 0, 0, 2, -1, -1, -2, 0, -1],
            [0, 0, 1, -5, 0, 0, -2, 0, 0, -2, 0, -1],
            [0, 0, 3, -5, 0, 0, -2, 0, 0, 2, 0, 1],
            [1, 0, 0, 0, 0, -2, -1, 0, 0, -1, 0, 0],
            [0, 0, 0, 3, 0, 0, -1, 0, 0, 1, 0, 0],
            [0, 0, 2, -6, 0, 0, 1, 0, 0, 1, 0, 0],
            [0, 0, 2, -4, 0, 0, 1, 0, 0, -1, 0, 0],
            [0, 0, 0, 0, 2, 0, -1, 0, 0, 1, 0, 0],
            [0, 0, 1, 0, 0, -2, 1, 0, 0, 0, 0, 0]
        );

        foreach ($componentsVelSun as $component) {
            $A = $l2 * $component[0]
                + $l3 * $component[1]
                + $l5 * $component[2]
                + $l6 * $component[3]
                + $l7 * $component[4]
                + $l8 * $component[5];
            $ARad = $A;

            $X += $component[6] * sin($ARad) + $component[7] * cos($ARad);
            $Y += $component[8] * sin($ARad) + $component[9] * cos($ARad);
            $Z += $component[10] * sin($ARad) + $component[11] * cos($ARad);
        }

        // Velocity components of the earth
        $componentsVelEarth = array(
            [1, 0, 0, 0, 0, 715, -656, -285],
            [0, 0, 0, 0, 1, 0, 26, -59],
            [1, 0, 0, 1, 0, 39, -36, -16],
            [1, 2, 0, -1, 0, 8, -7, -3],
            [1, -2, 0, 0, 0, 5, -5, -2],
            [1, 2, 0, 0, 0, 4, -4, -2],
            [0, 0, 0, 1, 1, 0, 1, -3],
            [1, -2, 0, 1, 0, -2, 2, 1],
            [1, 0, 0, 2, 0, 2, -2, -1],
            [0, 2, 0, 0, -1, 0, 1, -2],
            [1, 0, 0, 0, -2, -1, 1, 1],
            [1, 0, 1, 0, 0, -1, 1, 0],
            [1, 0, -1, 0, 0, 1, -1, 0],
            [1, 4, 0, -2, 0, 1, -1, 0],
            [1, -2, 0, 2, 0, -1, 1, 0],
            [1, 2, 0, 1, 0, 1, 0, 0],
            [0, 2, 0, -1, 1, 0, 0, -1]
        );

        foreach ($componentsVelEarth as $component) {
            $A = $w * $component[0]
                + $D * $component[1]
                + $ls * $component[2]
                + $l * $component[3]
                + $F * $component[4];
            $ARad = $A;

            $X += $component[5] * sin($ARad);
            $Y += $component[6] * cos($ARad);
            $Z += $component[7] * cos($ARad);
        }

        $dRaRad = ($Y * cos($raRad) - $X * sin($raRad)) / ($c * cos($dRad));
        $dRa = rad2deg($dRaRad);
        $dDRad = -1 * (($X * cos($raRad) + $Y * sin($raRad)) * sin($dRad) - $Z * cos($dRad)) / $c;
        $dD = rad2deg($dDRad);

        $rightAscension += $dRa;
        $declination += $dD;

        return new GeocentricEquatorialCoordinates($rightAscension, $declination, $radiusVector);
    }
}
