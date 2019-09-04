<?php

namespace Andrmoel\AstronomyBundle\Events\SolarEclipse;

use Andrmoel\AstronomyBundle\Location;

class BesselianElements
{
    private $tMax = 0.0;
    private $t0 = 0.0;
    private $dT = 0.0; // Delta T

    private $x = array();
    private $y = array();
    private $d = array();
    private $l1 = array();
    private $l2 = array();
    private $mu = array();

    private $tanF1 = 0.0;
    private $tanF2 = 0.0;

    private $latGreatestEclipse = 0.0;
    private $lonGreatestEclipse = 0.0;

    public function __construct(array $data)
    {
        $this->tMax = $data['tMax'];
        $this->t0 = $data['t0'];

        $this->dT = $data['dT'];

        $this->x = $data['x'];
        $this->y = $data['y'];
        $this->d = $data['d'];
        $this->l1 = $data['l1'];
        $this->l2 = $data['l2'];
        $this->mu = $data['mu'];

        $this->tanF1 = $data['tanF1'];
        $this->tanF2 = $data['tanF2'];

        $this->latGreatestEclipse = $data['latGreatestEclipse'];
        $this->lonGreatestEclipse = $data['lonGreatestEclipse'];
    }

    public function getTMax()
    {
        return $this->tMax;
    }

    public function getT0()
    {
        return $this->t0;
    }

    public function getDeltaT()
    {
        return $this->dT;
    }

    public function getX($t)
    {
        return $this->evaluate($this->x, $t);
    }

    public function getDX($t)
    {
        return $this->evaluateD($this->x, $t);
    }

    public function getY($t)
    {
        return $this->evaluate($this->y, $t);
    }

    public function getDY($t)
    {
        return $this->evaluateD($this->y, $t);
    }

    public function getD($t)
    {
        return $this->evaluate($this->d, $t);
    }

    public function getDD($t)
    {
        return $this->evaluateD($this->d, $t);
    }

    public function getMu($t)
    {
        return $this->evaluate($this->mu, $t);
    }

    public function getDMu($t)
    {
        return $this->evaluateD($this->mu, $t);
    }

    public function getL1($t)
    {
        return $this->evaluate($this->l1, $t);
    }

    public function getDL1($t)
    {
        return $this->evaluateD($this->l1, $t);
    }

    public function getL2($t)
    {
        return $this->evaluate($this->l2, $t);
    }

    public function getDL2($t)
    {
        return $this->evaluateD($this->l2, $t);
    }

    public function getTanF1()
    {
        return $this->tanF1;
    }

    public function getTanF2()
    {
        return $this->tanF2;
    }

    public function getLatitudeGreatestEclipse()
    {
        return $this->latGreatestEclipse;
    }

    public function getLongitudeGreatestEclipse()
    {
        return $this->lonGreatestEclipse;
    }

    private function evaluate($dataArray, $t)
    {
        $result = 0.0;

        foreach ($dataArray as $key => $n) {
            $result += $n * pow($t, $key);
        }

        return $result;
    }

    private function evaluateD($dataArray, $t)
    {
        $result = 0.0;

        foreach ($dataArray as $key => $n) {
            if ($key >= 1) {
                $result += $key * $n * pow($t, $key - 1);
            }
        }

        return $result;
    }
}
