<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

class Saturn extends Planet
{
    public function getVSOP87Data(): array
    {
        $data = file_get_contents(__DIR__ . '/../../Resources/vsop87/saturn.json');

        return json_decode($data, 1);
    }
}
