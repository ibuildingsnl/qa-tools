<?php
/**
 * @author Matthijs van den Bos <matthijs@vandenbos.org>
 * @copyright 2013 Matthijs van den Bos
 */

namespace Ibuildings\QA\Tools\PHP\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Run the Ibuildings QA Tools for PHP')
            ->setHelp('Runs all configured tools with Scrutinizer and PHPUnit')
            ->addOption('only-phpunit', 'op', InputOption::VALUE_OPTIONAL)
            ->addOption('only-scrutinizer', 'os', InputOption::VALUE_OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('only-phpunit')) {
            passthru(PACKAGE_BASE_DIR . '/bin/phpunit -c ' . BASE_DIR . '/phpunit.xml');
        } elseif ($input->getOption('only-scrutinizer')) {
            passthru(PACKAGE_BASE_DIR . '/bin/scrutinizer run ' . BASE_DIR);
        } else {
            passthru(PACKAGE_BASE_DIR . '/bin/scrutinizer run ' . BASE_DIR);
            passthru(PACKAGE_BASE_DIR . '/bin/phpunit -c ' . BASE_DIR . '/phpunit.xml');
        }
    }
}
