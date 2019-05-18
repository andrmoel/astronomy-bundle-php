<?php

namespace Andrmoel\AstronomyBundle\Parsers;

use Andrmoel\AstronomyBundle\Events\SolarEclipse\BesselianElements;

class BesselianElementsParser extends AbstractParser
{
    public function getParsedData(): BesselianElements
    {
        $polynomials = $this->parsePolynomials();
        $tanF1F2 = $this->parseTanF1F2();

        $data = [
            'tMax' => $this->parseTMax(),
            'dT' => $this->parseDeltaT(),
            't0' => $this->parseT0(),
            'tanF1' => $tanF1F2['tanF1'],
            'tanF2' => $tanF1F2['tanF2'],
        ];

        $data = array_merge_recursive($data, $polynomials);

        return new BesselianElements($data);
    }

    private function parseTMax(): float
    {
        $pattern = '/Instant of.*J[.]?D[.]? = ([0-9.]+)/si';

        if (preg_match($pattern, $this->data, $matches)) {
            return floatval($matches[1]);
        }

        return 0.0;
    }

    private function parseDeltaT(): float
    {
        $pattern = '/ΔT =.*?([0-9.]+).*?s/si';

        if (preg_match($pattern, $this->data, $matches)) {
            return floatval($matches[1]);
        }

        return 0.0;
    }

    private function parseT0(): float
    {
        $pattern = '/t0 =.*?([0-9.]+).*?TDT/si';

        if (preg_match($pattern, $this->data, $matches)) {
            return floatval($matches[1]);
        }

        return 0.0;
    }

    private function parsePolynomials(): array
    {
        $polynomials = [
            'x' => [],
            'y' => [],
            'd' => [],
            'l1' => [],
            'l2' => [],
            'mu' => [],
        ];

        /*
         * Structure for Polynomial Besselian Elements given by NASA
         *
         * 0  -0.129576   0.485417   11.86697   0.542112  -0.004025   89.24545
         * 1   0.5406409 -0.1416394  -0.013622  0.0001241  0.0001235  15.003937
         * 2  -0.0000293 -0.0000905  -0.000002 -0.0000118 -0.0000117
         * 3  -0.0000081  0.0000021
         */

        $rows = explode("\n", $this->data);

        $pattern = '/[0-9]{1} +[0-9.-]+ +[0-9.-]+/si';
        foreach ($rows as $row) {
            if (preg_match($pattern, $row)) {
                $parts = preg_split('/\s+/', $row, -1, PREG_SPLIT_NO_EMPTY);

                $index = $parts[0];

                $polynomials['x'][$index] = isset($parts[1]) ? floatval($parts[1]) : 0.0;
                $polynomials['y'][$index] = isset($parts[2]) ? floatval($parts[2]) : 0.0;
                $polynomials['d'][$index] = isset($parts[3]) ? floatval($parts[3]) : 0.0;
                $polynomials['l1'][$index] = isset($parts[4]) ? floatval($parts[4]) : 0.0;
                $polynomials['l2'][$index] = isset($parts[5]) ? floatval($parts[5]) : 0.0;
                $polynomials['mu'][$index] = isset($parts[6]) ? floatval($parts[6]) : 0.0;
            }
        }

        return $polynomials;
    }

    private function parseTanF1F2(): array
    {
        $pattern = '/Tan [f|ƒ]1 = ([0-9.]+).*?Tan [f|ƒ]2 = ([0-9.]+)/si';

        if (preg_match($pattern, $this->data, $matches)) {
            return [
                'tanF1' => floatval($matches[1]),
                'tanF2' => floatval($matches[2]),
            ];
        }

        return [
            'tanF1' => 0.0,
            'tanF2' => 0.0,
        ];
    }
}
