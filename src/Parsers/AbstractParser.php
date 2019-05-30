<?php

namespace Andrmoel\AstronomyBundle\Parsers;

abstract class AbstractParser
{
    protected $data = '';

    abstract public function getParsedData();

    public function setData(string $fileOrData): void
    {
        if (file_exists($fileOrData)) {
            $this->data = file_get_contents($fileOrData);
        } else {
            $this->data = $fileOrData;
        }
    }
}
