<?php

namespace Andrmoel\AstronomyBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class AndrmoelAstronomyExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        var_dump("fofo");die();
    }
}
