<?php

namespace Andrmoel\AstronomyBundle;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Planets\Earth;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Sun;
use Andrmoel\AstronomyBundle\Coordinates\GeocentricEquatorialCoordinates;
use Andrmoel\AstronomyBundle\Utils\AngleUtil;

class Corrections
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

    public function correctEffectOfNutation(
        GeocentricEquatorialCoordinates $geoEquCoordinates
    ): GeocentricEquatorialCoordinates
    {
        $rightAscension = $geoEquCoordinates->getRightAscension();
        $declination = $geoEquCoordinates->getDeclination();
        $radiusVector = $geoEquCoordinates->getRadiusVector();

        $dEps = $this->earth->getNutationInObliquity();
        $eps = $this->earth->getObliquityOfEcliptic();
        $dPhi = $this->earth->getNutation();

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

    public function correctEffectOfAberration(float $rightAscension, float $declination)
    {
        $k = Constants::CONSTANT_OF_ABERRATION;
        $e = $this->earth->getEccentricity();
        $eps = $this->earth->getObliquityOfEcliptic();
        $pi = $this->earth->getLongitudeOfPerihelionOfOrbit();
        $o = $this->sun->getTrueLongitude();


        $raRad = deg2rad($rightAscension);
        $dRad = deg2rad($declination);
        $epsRad = deg2rad($eps);
        $piRad = deg2rad($pi);
        $oRad = deg2rad($o);

        // Meeus 23.3
        $dRa2 = -$k * ((cos($raRad) * cos($oRad) * cos($epsRad) + sin($raRad) * sin($oRad)) / cos($dRad))
            + $e * $k * ((cos($raRad) * cos($piRad) * cos($epsRad) + sin($raRad) * sin($piRad)) / cos($dRad));

        $dD2 = -$k * (
                cos($oRad) * cos($epsRad) * (tan($epsRad) * cos($dRad) - sin($raRad) * sin($dRad))
                + cos($raRad) * sin($dRad) * sin($oRad)
            )
            + $e * $k * (
                cos($piRad) * cos($epsRad) * (tan($epsRad) * cos($dRad) - sin($raRad) * sin($dRad))
                + cos($raRad) * sin($dRad) * sin($piRad)
            );

        // TODO ...
        var_dump(AngleUtil::dec2angle($dRa2), AngleUtil::dec2angle($dD2));
        die();
    }
}
