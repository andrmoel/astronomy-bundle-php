<?php

namespace Andrmoel\AstronomyBundle\Eclipses;

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


    /**
     * BesselianElements constructor.
     * @param array $data
     */
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
    }


    /**
     * Load besselian elements from string
     * @param $string
     */
    public function loadBesselianElementsFromString($string)
    {
        // TODO ...
    }


    /**
     * Get time of greatest eclipse
     * @return float
     */
    public function getTMax()
    {
        return $this->tMax;
    }


    /**
     * Get t0
     * @return float
     */
    public function getT0()
    {
        return $this->t0;
    }


    /**
     * Get delta t
     * @return float
     */
    public function getDeltaT()
    {
        return $this->dT;
    }


    /**
     * Get x
     * @param $t
     * @return float
     */
    public function getX($t)
    {
        return $this->evaluate($this->x, $t);
    }


    /**
     * Get dx
     * @param $t
     * @return float
     */
    public function getDX($t)
    {
        return $this->evaluateD($this->x, $t);
    }


    /**
     * Get y
     * @param $t
     * @return float
     */
    public function getY($t)
    {
        return $this->evaluate($this->y, $t);
    }


    /**
     * Get dy
     * @param $t
     * @return float
     */
    public function getDY($t)
    {
        return $this->evaluateD($this->y, $t);
    }


    /**
     * Get d
     * @param $t
     * @return float
     */
    public function getD($t)
    {
        return $this->evaluate($this->d, $t);
    }


    /**
     * Get dd
     * @param $t
     * @return float
     */
    public function getDD($t)
    {
        return $this->evaluateD($this->d, $t);
    }


    /**
     * Get m
     * @param $t
     * @return float
     */
    public function getMu($t)
    {
        return $this->evaluate($this->mu, $t);
    }


    /**
     * Get dd
     * @param $t
     * @return float
     */
    public function getDMu($t)
    {
        return $this->evaluateD($this->mu, $t);
    }


    /**
     * Get l1
     * @param $t
     * @return float
     */
    public function getL1($t)
    {

        return $this->evaluate($this->l1, $t);
    }


    /**
     * Get dl1
     * @param $t
     * @return float
     */
    public function getDL1($t)
    {
        return $this->evaluateD($this->l1, $t);
    }


    /**
     * Get l2
     * @param $t
     * @return float
     */
    public function getL2($t)
    {

        return $this->evaluate($this->l2, $t);
    }


    /**
     * Get dl2
     * @param $t
     * @return float
     */
    public function getDL2($t)
    {
        return $this->evaluateD($this->l2, $t);
    }


    /**
     * Get tan f1
     * @return float
     */
    public function getTanF1()
    {
        return $this->tanF1;
    }


    /**
     * Get tan f2
     * @return float
     */
    public function getTanF2()
    {
        return $this->tanF2;
    }


    /**
     * Evaluate
     * @param $dataArray
     * @param $t
     * @return float
     */
    private function evaluate($dataArray, $t)
    {
        $result = 0.0;

        foreach ($dataArray as $key => $n) {
            $result += $n * pow($t, $key);
        }

        return $result;
    }


    /**
     * Evaluate D
     * @param $dataArray
     * @param $t
     * @return float
     */
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
