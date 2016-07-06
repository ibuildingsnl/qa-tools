<?php

namespace Ibuildings\QaTools\Core\Tool;

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
            new FileLocator(APPLICATION_ROOT_DIR . '/' . $resourcePath . '/config')
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
        $fqcn = explode('\\', get_called_class());
        $subNamespaces = array_splice($fqcn, 2, -1);

        return implode($subNamespaces, '/') . '/Resources';
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
