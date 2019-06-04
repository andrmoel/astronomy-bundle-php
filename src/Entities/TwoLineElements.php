<?php

namespace Andrmoel\AstronomyBundle\Entities;

class TwoLineElements
{
    private $satelliteNo = '';
    private $classification = '';
    private $internationalDesignator = '';
    private $epoch = null;
    private $td1MeanMotion = 0.0;
    private $td2MeanMotion = 0.0;
    private $BSTARDragTerm = 0.0;
    private $setNumber = 0;
    private $inclination = 0.0;
    private $rightAscensionOfAscendingNode = 0.0;
    private $eccentricity = 0.0;
    private $argumentOfPerigee = 0.0;
    private $meanAnomaly = 0.0;
    private $meanMotion = 0.0;
    private $revolutionNoAtEpoch = 0;

    public function __construct(array $tleData)
    {
        foreach ($tleData as $var => $value) {
            $this->$var = $value;
        }
    }

    public static function createFromTLEData(array $tleData)
    {

    }
}
