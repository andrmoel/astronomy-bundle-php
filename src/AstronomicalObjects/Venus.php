<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects;

class Mercury extends AstronomicalObject
{
    private $argumentsL0 = [
        [317614667, 0, 0],
        [2, 0, -1, 0, 1274027, -3699111],
    ];

    // Sum parameters
    private $sumL = 0;
    private $sumR = 0;
    private $sumB = 0;

    public function getEclipticalLongitude()
    {

    }
}
