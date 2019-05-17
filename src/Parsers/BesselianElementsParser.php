<?php

namespace Andrmoel\AstronomyBundle\Parsers;

use Andrmoel\AstronomyBundle\Events\SolarEclipse\BesselianElements;

class BesselianElementsParser extends AbstractParser
{
    public function getParsedData(): BesselianElements
    {
        $tanF1F2 = $this->parseTanF1F2();

        $data = [
            'dT' => $this->parseDeltaT(),
            't0' => $this->parseT0(),
            'tanF1' => $tanF1F2['tanF1'],
            'tanF2' => $tanF1F2['tanF2'],
        ];

        var_dump($data);die();

        return new BesselianElements($data);
    }

    private function parseDeltaT(): float
    {
        $pattern = '/ΔT =.*?([0-9.]+).*?s/si';

        if(preg_match($pattern, $this->data, $matches)) {
            return floatval($matches[1]);
        }

        return 0.0;
    }

    private function parseT0(): float
    {
        $pattern = '/t0 =.*?([0-9.]+).*?TDT/si';

        if(preg_match($pattern, $this->data, $matches)) {
            return floatval($matches[1]);
        }

        return 0.0;
    }

    private function parsePolynomials(): array
    {
        $pattern = '';

        return [];
    }

    private function parseTanF1F2(): array
    {
        $pattern = '/Tan ƒ1 = ([0-9.]+).*?Tan ƒ2 = ([0-9.]+)/si';

        if(preg_match($pattern, $this->data, $matches)) {
            return [
                'tanF1' => floatval($matches[1]),
                'tanF2' => floatval($matches[2]),
            ];
        }

        return null;
    }
}
