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
            ->setHelp('Runs all configured tools with Scrutinizer and PHPUnit');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        passthru(BASE_DIR . '/bin/scrutinizer run ' . BASE_DIR);
        passthru(BASE_DIR . '/bin/phpunit -c ' . BASE_DIR . '/phpunit.xml');
    }
}
