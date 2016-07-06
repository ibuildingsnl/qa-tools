<?php

namespace Ibuildings\QaTools\Core\Application;

use Ibuildings\QaTools\Core\Application\Compiler\RegisterConfiguratorsCompilerPass;
use Ibuildings\QaTools\Core\IO\File\FilesystemAdapter;
use Ibuildings\QaTools\Core\Tool\Tool;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Filesystem\Filesystem;

final class ContainerLoader
{
    public static function load(Application $application, $isDebug)
    {
        $file = __DIR__ . '/../../../var/cache/container.php';
        $precompiledContainer = new ConfigCache($file, $isDebug);

        if (!$precompiledContainer->isFresh() || $isDebug) {
            $containerBuilder = new ContainerBuilder();
            $containerBuilder->addCompilerPass(new RegisterConfiguratorsCompilerPass);

            $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../Resources/config/'));
            $loader->load('config.yml');
            $loader->load('services.yml');

            /** @var Tool $tool */
            foreach ($application->getRegisteredTools() as $tool) {
                $tool->boot($containerBuilder);
            }

            $containerBuilder->compile();

            // Save the compiled container
            $dumper      = new PhpDumper($containerBuilder);
            $compiledContainer = $dumper->dump([
                'class' => 'CompiledContainer',
                'namespace' => 'Ibuildings\QaTools\Core\Application'
            ]);

            $fileHandler = new FilesystemAdapter(new Filesystem());
            $fileHandler->writeTo($compiledContainer, $file);
        }

        require_once $file;

        return new CompiledContainer();
    }
}
