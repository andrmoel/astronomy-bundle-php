<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

class Uranus extends Planet
{
    public function loadVSOP87Data(): array
    {
        $data = file_get_contents(self::VSOP87_FILE_PATH . 'uranus.json');

        return json_decode($data, 1);
    }
}
