<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

class Uranus extends Planet
{
    public function loadVSOP87Data(): array
    {
        $data = file_get_contents(__DIR__ . '/../../Resources/vsop87/uranus.json');

        return json_decode($data, 1);
    }
}
