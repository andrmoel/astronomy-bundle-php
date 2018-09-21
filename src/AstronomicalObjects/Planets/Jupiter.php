<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

class Jupiter extends Planet
{
    public function getVSOP87Data(): array
    {
        // TODO Ecliptical longitude falsch
        $data = file_get_contents(__DIR__ . '/../../Resources/vsop87/jupiter.json');

        return json_decode($data, 1);
    }
}
