<?php

namespace Ibuildings\QaTools\Core\Tool;

use ReflectionClass;

/**
 * This class is abstract as these are defaults that can be overwritten by an implementation.
 */
abstract class Tool
{
    /**
     * Gets the base config path in which the tools' configuration files should be located.
     *
     * @return string
     */
    public function getConfigPath()
    {
        $reflector = new ReflectionClass(get_called_class());

        return dirname($reflector->getFileName()) . '/Resources/config';
    }

    /**
     * Gets the files necessary to configure the tool.
     *
     * @return string[]
     */
    public function getConfigFiles()
    {
        return [
            'configurators.yml'
        ];
    }
}
