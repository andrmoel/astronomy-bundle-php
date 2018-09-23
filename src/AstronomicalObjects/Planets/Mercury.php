<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

class Mercury extends Planet
{
    public function loadVSOP87Data(): array
    {
        $data = file_get_contents(self::VSOP87_FILE_PATH . 'mercury.json');

        return json_decode($data, 1);
    }
}
