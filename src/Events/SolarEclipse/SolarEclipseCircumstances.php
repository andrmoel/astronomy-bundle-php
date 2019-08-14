<?php

namespace Andrmoel\AstronomyBundle\Events\SolarEclipse;

use Andrmoel\AstronomyBundle\Coordinates\LocalHorizontalCoordinates;
use Andrmoel\AstronomyBundle\Corrections\LocalHorizontalCorrections;

class SolarEclipseCircumstances
{
    private $eclipseType;
    private $t;

    private $sinD;
    private $cosD;
    private $sinH;
    private $cosH;

    private $eta;

    private $u;
    private $v;
    private $a;
    private $b;
    private $l1s;
    private $l2s;
    private $n2;

    private $p;
    private $alt;
    private $q;
    private $v_pos;
    private $m;
    private $magnitude;
    private $moonSunRatio;

    private $sunAltitude;
    private $sunAzimuth;

    public function setEclipseType(string $eclipseType)
    {
        $this->eclipseType = $eclipseType;
    }

    public function getEclipseType(): string
    {
        return $this->eclipseType;
    }

    public function setT($t)
    {
        $this->t = $t;
    }

    public function getT()
    {
        return $this->t;
    }

    public function setSinD($sinD)
    {
        $this->sinD = $sinD;
    }

    public function getSinD()
    {
        return $this->sinD;
    }

    public function setCosD($cosD)
    {
        $this->cosD = $cosD;
    }

    public function getCosD()
    {
        return $this->cosD;
    }

    public function setSinH($sinH)
    {
        $this->sinH = $sinH;
    }

    public function getSinH()
    {
        return $this->sinH;
    }

    public function setCosH($cosH)
    {
        $this->cosH = $cosH;
    }

    public function getCosH()
    {
        return $this->cosH;
    }

    public function setEta($eta)
    {
        $this->eta = $eta;
    }

    public function getEta()
    {
        return $this->eta;
    }

    public function setU($u)
    {
        $this->u = $u;
    }

    public function getU()
    {
        return $this->u;
    }

    public function setV($v)
    {
        $this->v = $v;
    }

    public function getV()
    {
        return $this->v;
    }

    public function setA($a)
    {
        $this->a = $a;
    }

    public function getA()
    {
        return $this->a;
    }

    public function setB($b)
    {
        $this->b = $b;
    }

    public function getB()
    {
        return $this->b;
    }

    public function setL1s($l1s)
    {
        $this->l1s = $l1s;
    }

    public function getL1s()
    {
        return $this->l1s;
    }

    public function setL2s($l2s)
    {
        $this->l2s = $l2s;
    }

    public function getL2s()
    {
        return $this->l2s;
    }

    public function setN2($n2)
    {
        $this->n2 = $n2;
    }

    public function getN2()
    {
        return $this->n2;
    }

    public function setM($m)
    {
        $this->m = $m;
    }

    public function getM()
    {
        return $this->m;
    }

    public function setMagnitude($magnitude)
    {
        $this->magnitude = $magnitude;
    }

    public function getMagnitude()
    {
        return $this->magnitude;
    }

    public function setMoonSunRatio($moonSunRatio)
    {
        $this->moonSunRatio = $moonSunRatio;
    }

    public function getMoonSunRatio()
    {
        return $this->moonSunRatio;
    }

    public function setSunAltitude($sunAltitude)
    {
        $this->sunAltitude = $sunAltitude;
    }

    public function getSunAltitude()
    {
        return rad2deg($this->sunAltitude);
    }

    public function setSunAzimuth($sunAzimuth)
    {
        $this->sunAzimuth = $sunAzimuth;
    }

    public function getSunAzimuth()
    {
        return rad2deg($this->sunAzimuth);
    }

    public function getLocalHorizontalCoordinates(bool $refraction = true): LocalHorizontalCoordinates
    {
        $azimuth = rad2deg($this->sunAzimuth);
        if ($azimuth < 0) {
            $azimuth = 360 + $azimuth;
        }
        $altitude = rad2deg($this->sunAltitude);

        $locHorCoord = new LocalHorizontalCoordinates($azimuth, $altitude);

        if ($refraction) {
            $locHorCoord = LocalHorizontalCorrections::correctAtmosphericRefraction($locHorCoord);
        }

        return $locHorCoord;
    }
}
