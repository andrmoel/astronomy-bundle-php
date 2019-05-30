<?php

namespace Andrmoel\AstronomyBundle\Parsers;

class VSOP87Parser extends AbstractParser
{
    public function getParsedData()
    {
        $data = [];

        $pattern = '/^ [0-5][1-8]([1-3])([0-9]) /si';

        $rows = explode("\n", $this->data);
        foreach ($rows as $row) {
            if (preg_match($pattern, $row, $matches)) {
                $coefficient = $matches[1];
                $tIndex = $matches[2];

                $A = trim(substr($row, 80, 97 - 80));
                $B = trim(substr($row, 98, 111 - 98));
                $C = trim(substr($row, 112, 131 - 112));

                $data[$coefficient][$tIndex][] = [
                    'A' => $A,
                    'B' => $B,
                    'C' => $C,
                ];
            }
        }

        return $data;
    }
}
