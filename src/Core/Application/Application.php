<?php

namespace Ibuildings\QaTools\Core\Application;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Tool\Tool;
use Ibuildings\QaTools\Tool\PhpMd\PhpMd;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class Application extends ConsoleApplication
{
    const NAME = 'Ibuildings QA Tools';

    /** The version of the application, replaced automatically on build */
    const VERSION = '@package_version@';

    /**
     * @var bool
     */
    private $isDebug;

    /**
     * @return Tool[]
     */
    public function getRegisteredTools()
    {
        return [
            new PhpMd(),
        ];
    }

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct($isDebug)
    {
        Assertion::boolean($isDebug);

        parent::__construct(self::NAME, self::VERSION);
        define('APPLICATION_ROOT_DIR', __DIR__ . '/../../..');

        $this->isDebug = $isDebug;
    }

    public function boot()
    {
        $this->container = ContainerLoader::load($this, $this->isDebug);
    }

    /**
     * @param InputInterface|null $input
     * @param OutputInterface|null $output
     * @return integer
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        if ($this->isDebug) {
            $output = $this->setDebugMode($output);
        }

        $commands = $this->all();

        /** @var Command $command */
        foreach ($commands as $command) {
            if ($command instanceof ContainerAwareInterface) {
                $command->setContainer($this->container);
            }
        }

        return parent::run($input, $output);
    }

    /**
     * @param OutputInterface $output
     * @return ConsoleOutput|OutputInterface
     */
    protected function setDebugMode(OutputInterface $output = null)
    {
        if ($output === null) {
            $output = new ConsoleOutput();
        }

        $output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);

        return $output;
    }
}
