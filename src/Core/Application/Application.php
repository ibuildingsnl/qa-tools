<?php

namespace Ibuildings\QaTools\Core\Application;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Tool\Tool;
use Ibuildings\QaTools\Tool\Behat\Behat;
use Ibuildings\QaTools\Tool\PhpCs\PhpCs;
use Ibuildings\QaTools\Tool\PhpParallelLint\PhpParallelLint;
use Ibuildings\QaTools\Tool\PhpMd\PhpMd;
use Ibuildings\QaTools\Tool\PhpUnit\PhpUnit;
use Ibuildings\QaTools\Tool\SensioLabsSecurityChecker\SensioLabsSecurityChecker;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) -- Due to wide-spread value object usage a higher coupling is
 *     acceptable
 */
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
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param bool $isDebug
     */
    public function __construct($isDebug)
    {
        Assertion::boolean($isDebug);

        parent::__construct(self::NAME, self::VERSION);

        $this->isDebug = $isDebug;
    }

    /**
     * @return Tool[]
     */
    public function getRegisteredTools()
    {
        return [
            new PhpUnit(),
            new PhpParallelLint(),
            new PhpMd(),
            new PhpCs(),
            new SensioLabsSecurityChecker(),
            new Behat(),
        ];
    }

    public function boot()
    {
        $this->container = ContainerLoader::load($this, $this->isDebug);
    }

    /**
     * @param InputInterface|null  $input
     * @param OutputInterface|null $output
     * @return integer
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

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->container->set('logger', new ConsoleLogger($output));

        return parent::doRun($input, $output);
    }
}
