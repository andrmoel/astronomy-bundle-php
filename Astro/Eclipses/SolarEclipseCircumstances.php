<?php

namespace App\Util\Astro\Eclipses;

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


    /**
     * Set t
     * @param $t
     */
    public function setT($t)
    {
        $this->t = $t;
    }


    /**
     * Get t
     * @return float
     */
    public function getT()
    {
        return $this->t;
    }


    /**
     * Set sinD
     * @param $sinD
     */
    public function setSinD($sinD)
    {
        $this->sinD = $sinD;
    }


    /**
     * Get sinD
     * @return float
     */
    public function getSinD()
    {
        return $this->sinD;
    }


    /**
     * Set cosD
     * @param $cosD
     */
    public function setCosD($cosD)
    {
        $this->cosD = $cosD;
    }


    /**
     * Get cosD
     * @return float
     */
    public function getCosD()
    {
        return $this->cosD;
    }


    /**
     * Set sinH
     * @param $sinH
     */
    public function setSinH($sinH)
    {
        $this->sinH = $sinH;
    }


    /**
     * Get sinH
     * @return float
     */
    public function getSinH()
    {
        return $this->sinH;
    }


    /**
     * Set cosH
     * @param $cosH
     */
    public function setCosH($cosH)
    {
        $this->cosH = $cosH;
    }


    /**
     * Get cosD
     * @return float
     */
    public function getCosH()
    {
        return $this->cosH;
    }


    /**
     * Set eta
     * @param $eta
     */
    public function setEta($eta)
    {
        $this->eta = $eta;
    }


    /**
     * Get eta
     * @return float
     */
    public function getEta()
    {
        return $this->eta;
    }


    /**
     * Set u
     * @param $u
     */
    public function setU($u)
    {
        $this->u = $u;
    }


    /**
     * Get u
     * @return float
     */
    public function getU()
    {
        return $this->u;
    }


    /**
     * Set v
     * @param $v
     */
    public function setV($v)
    {
        $this->v = $v;
    }
    

    /**
     * Get v
     * @return float
     */
    public function getV()
    {
        return $this->v;
    }
    

    /**
     * Set a
     * @param $a
     */
    public function setA($a)
    {
        $this->a = $a;
    }


    /**
     * Get a
     * @return float
     */
    public function getA()
    {
        return $this->a;
    }


    /**
     * Set b
     * @param $b
     */
    public function setB($b)
    {
        $this->b = $b;
    }


    /**
     * Get b
     * @return float
     */
    public function getB()
    {
        return $this->b;
    }


    /**
     * Set l1s
     * @param $l1s
     */
    public function setL1s($l1s)
    {
        $this->l1s = $l1s;
    }


    /**
     * Get l1s
     * @return float
     */
    public function getL1s()
    {
        return $this->l1s;
    }

    
    /**
     * Set l2s
     * @param $l2s
     */
    public function setL2s($l2s)
    {
        $this->l2s = $l2s;
    }


    /**
     * Get l2s
     * @return float
     */
    public function getL2s()
    {
        return $this->l2s;
    }


    /**
     * Set n2
     * @param $n2
     */
    public function setN2($n2)
    {
        $this->n2 = $n2;
    }


    /**
     * Get n2
     * @return float
     */
    public function getN2()
    {
        return $this->n2;
    }


    /**
     * Set m
     * @param $m
     */
    public function setM($m)
    {
        $this->m = $m;
    }


    /**
     * Get m
     * @return float
     */
    public function getM()
    {
        return $this->m;
    }


    /**
     * Set magnitude
     * @param $magnitude
     */
    public function setMagnitude($magnitude)
    {
        $this->magnitude = $magnitude;
    }


    /**
     * Get magnitude
     * @return float
     */
    public function getMagnitude()
    {
        return $this->magnitude;
    }


    /**
     * Set moonSunRatio
     * @param $moonSunRatio
     */
    public function setMoonSunRatio($moonSunRatio)
    {
        $this->moonSunRatio = $moonSunRatio;
    }


    /**
     * Get moonSunRatio
     * @return float
     */
    public function getMoonSunRatio()
    {
        return $this->moonSunRatio;
    }


    /**
     * Set sun altitude
     * @param $sunAltitude
     */
    public function setSunAltitude($sunAltitude)
    {
        $this->sunAltitude = $sunAltitude;
    }


    /**
     * Get sun altitude
     * @return float
     */
    public function getSunAltitude()
    {
        return rad2deg($this->sunAltitude);
    }


    /**
     * Set sun azimuth
     * @param $sunAzimuth
     */
    public function setSunAzimuth($sunAzimuth)
    {
        $this->sunAzimuth = $sunAzimuth;
    }


    /**
     * Get sun azimuth
     * @return float
     */
    public function getSunAzimuth()
    {
        $azimuth = rad2deg($this->sunAzimuth);
        if ($azimuth < 0) {
            $azimuth = 360 + $azimuth;
        }

        return $azimuth;
    }
}
