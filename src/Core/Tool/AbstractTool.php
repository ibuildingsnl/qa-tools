<?php

namespace Ibuildings\QaTools\Core\Tool;

use ReflectionClass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

abstract class AbstractTool implements Tool
{
    public function build(ContainerBuilder $containerBuilder)
    {
        $resourcePath = $this->determineResourcePath();

        $containerBuilder->setParameter('tool.' . static::class . '.resource_path', $resourcePath);
        $configurationFileLoader = new YamlFileLoader(
            $containerBuilder,
            new FileLocator($resourcePath . '/config')
        );

        foreach ($this->getConfigurationFiles() as $configurationFile) {
            $configurationFileLoader->load($configurationFile);
        }
    }

    /**
     * @return string
     */
    protected function determineResourcePath()
    {
        $toolReflection = new ReflectionClass($this);
        $toolFilePath = $toolReflection->getFileName();
        $resourcesPath = dirname($toolFilePath) . '/Resources';

        return $resourcesPath;
    }

    /**
     * @return string[]
     */
    protected function getConfigurationFiles()
    {
        return [
            'configurators.yml'
        ];
    }
}
