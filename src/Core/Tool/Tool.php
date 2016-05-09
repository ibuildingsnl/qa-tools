<?php

namespace Ibuildings\QaTools\Core\Tool;

use ReflectionClass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

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

    /**
     * Boots the tool, adding its configuration to the container.
     *
     * @param ContainerBuilder $containerBuilder
     */
    public function boot(ContainerBuilder $containerBuilder)
    {
        $configFileLoader = new YamlFileLoader($containerBuilder , new FileLocator($this->getConfigPath()));

        /** @var string $configFile */
        foreach ($this->getConfigFiles() as $configFile) {
            $configFileLoader->load($configFile);
        }
    }
}
