<?php

namespace Ibuildings\QaTools\Core\Application;

use Ibuildings\QaTools\Core\Tool\Tool;
use Ibuildings\QaTools\Tool\PhpMd\PhpMd;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class Application extends ConsoleApplication
{
    const NAME = 'Ibuildings QA-tools';
    const VERSION = '@package_version@';

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct()
    {
        parent::__construct(self::NAME, self::VERSION);
    }

    /**
     * Instantiates and lists the tools that are configurable through the QA-tools in order to properly configure them.
     * @return Tool[]
     */
    public function getRegisteredTools()
    {
        return [
            new PhpMd(),
        ];
    }

    /**
     * Loads application and tool configurations.
     */
    public function boot()
    {
        $container = new ContainerBuilder();

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config/'));
        $loader->load('config.yml');
        $loader->load('services.yml');

        $tools = $this->getRegisteredTools();

        /** @var Tool $tool */
        foreach ($tools as $tool) {
            $configFileLoader = new YamlFileLoader($container, new FileLocator($tool->getConfigPath()));
            $configFiles = $tool->getConfigFiles();

            /** @var string $configFile */
            foreach ($configFiles as $configFile) {
                $configFileLoader->load($configFile);
            }
        }

        $container->compile();

        $this->container = $container;
    }

    /**
     * Makes commands that implement the ContainerAwareInterface container aware and runs the Symfony Console
     * Application.
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        $commands = $this->all();

        /** @var Command $command */
        foreach ($commands as $command) {
            if ($command instanceof ContainerAwareInterface) {
                $command->setContainer($this->container);
            }
        }

        return parent::run($input, $output);
    }
}
