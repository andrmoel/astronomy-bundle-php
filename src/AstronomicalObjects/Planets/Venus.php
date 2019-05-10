<?php

namespace Andrmoel\AstronomyBundle\AstronomicalObjects\Planets;

use Andrmoel\AstronomyBundle\AstronomicalObjects\AstronomicalObjectInterface;

class Venus extends Planet implements AstronomicalObjectInterface
{
    public function loadVSOP87Data(): array
    {
        $data = file_get_contents(self::VSOP87_FILE_PATH . 'venus.json');

        return json_decode($data, 1);
    }
}
