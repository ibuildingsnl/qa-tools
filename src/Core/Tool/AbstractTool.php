<?php

namespace Ibuildings\QaTools\Core\Tool;

use Ibuildings\QaTools\Core\Application\Basedir;
use ReflectionClass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractTool implements Tool
{
    public function build(ContainerBuilder $containerBuilder)
    {
        $resourcePath = $this->determineResourcePath();

        $containerBuilder->setParameter('tool.' . static::class . '.resource_path', $resourcePath);
        $configurationFileLoader = new YamlFileLoader(
            $containerBuilder,
            new FileLocator(Basedir::get() . '/' . $resourcePath . '/config')
        );

        foreach ($this->getConfigurationFiles() as $configurationFile) {
            $configurationFileLoader->load($configurationFile);
        }
    }

    /**
     * Returns the path to this tool's resources relative to the application
     * entrypoint `./bin/qa-tools`.
     *
     * @return string
     */
    protected function determineResourcePath()
    {
        $toolReflection = new ReflectionClass($this);
        $toolFilePath = $toolReflection->getFileName();
        $absoluteResourcesPath = dirname($toolFilePath) . '/Resources';

        // dirname() is used to traverse up several directories. `../` directories
        // are not supported by Filesystem::makePathRelative().
        $projectDirectory = dirname(dirname(dirname(__DIR__)));

        $fs = new Filesystem();
        $relativeResourcesPath = $fs->makePathRelative(
            $absoluteResourcesPath,
            $projectDirectory . '/bin'
        );

        return $relativeResourcesPath;
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
