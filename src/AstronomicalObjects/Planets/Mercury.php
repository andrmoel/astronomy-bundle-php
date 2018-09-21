<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

class Mercury extends Planet
{
    public function loadVSOP87Data(): array
    {
        $data = file_get_contents(__DIR__ . '/../../Resources/vsop87/mercury.json');

        return json_decode($data, 1);
    }
}
