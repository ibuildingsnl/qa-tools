<?php

namespace Ibuildings\QaTools\Core\Application;

use Ibuildings\QaTools\Core\Application\Compiler\RegisterConfiguratorsCompilerPass;
use Ibuildings\QaTools\Core\Application\Compiler\RegisterTaskExecutorsCompilerPass;
use Ibuildings\QaTools\Core\IO\File\FilesystemFileHandler;
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
            $containerBuilder->addCompilerPass(new RegisterConfiguratorsCompilerPass());
            $containerBuilder->addCompilerPass(new RegisterTaskExecutorsCompilerPass());
            $containerBuilder->setParameter('application_root_dir', realpath(__DIR__ . '/../../..'));

            $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../Resources/config/'));
            $loader->load('config.yml');
            $loader->load('services.yml');
            $loader->load('task_executors.yml');

            /** @var Tool $tool */
            foreach ($application->getRegisteredTools() as $tool) {
                $tool->build($containerBuilder);
            }

            $containerBuilder->compile();

            // Save the compiled container
            $dumper = new PhpDumper($containerBuilder);
            $compiledContainer = $dumper->dump([
                'class' => 'CompiledContainer',
                'namespace' => 'Ibuildings\QaTools\Core\Application'
            ]);

            $fileHandler = new FilesystemFileHandler(new Filesystem());
            $fileHandler->writeTo($file, $compiledContainer);
        }

        require_once $file;

        return new CompiledContainer();
    }
}
