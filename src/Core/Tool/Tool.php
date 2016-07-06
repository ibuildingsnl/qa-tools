<?php

namespace Ibuildings\QaTools\Core\Tool;

use Symfony\Component\DependencyInjection\ContainerBuilder;

interface Tool
{
    /**
     * @param ContainerBuilder $containerBuilder
     * @void
     */
    public function build(ContainerBuilder $containerBuilder);
}
