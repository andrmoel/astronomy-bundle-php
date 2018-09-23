<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

class Mars extends Planet
{
    public function loadVSOP87Data(): array
    {
        $data = file_get_contents(self::VSOP87_FILE_PATH . 'mars.json');

        return json_decode($data, 1);
    }
}
