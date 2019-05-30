<?php

namespace Andrmoel\AstronomyBundle\Parsers;

class ParserFactory
{
    public static function get(string $className, string $data): AbstractParser
    {
        if (class_exists($className)) {
            /** @var AbstractParser $parser */
            $parser = new $className();
            $parser->setData($data);

            return $parser;
        }

        return null;
    }
}
