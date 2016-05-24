<?php

namespace Ibuildings\QaTools\Core\Application;

use Ibuildings\QaTools\Core\Application\Compiler\RegisterConfiguratorsCompilerPass;
use Ibuildings\QaTools\Core\Tool\Tool;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class ContainerLoader
{
    public static function loadAndCacheFor(Application $application)
    {
        // If @package_version@ has not been replaced, we are in debug/dev mode.
        $isDebug = strpos(Application::VERSION, 'package_version') !== false;

        $file = __DIR__ . '/../../../precompiled/container.php';
        $precompiledContainer = new ConfigCache($file, $isDebug);

        if ($precompiledContainer->isFresh() && !$isDebug) {
            require_once $file;
            return new \PrecompiledContainer();
        }

        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addCompilerPass(new RegisterConfiguratorsCompilerPass);

        $loader = new YamlFileLoader($containerBuilder , new FileLocator(__DIR__ . '/../Resources/config/'));
        $loader->load('config.yml');
        $loader->load('services.yml');

        /** @var Tool $tool */
        foreach ($application->getRegisteredTools() as $tool) {
            $tool->boot($containerBuilder);
        }

        $containerBuilder->compile();

        // Save the compiled container
        $dumper = new PhpDumper($containerBuilder);
        file_put_contents($file, $dumper->dump(['class' => 'PrecompiledContainer']));

        return $containerBuilder;
    }
}
