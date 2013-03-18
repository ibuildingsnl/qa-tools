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
use Symfony\Component\Process\Process;

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

    public function outputBuffer($type, $buffer)
    {
        echo $buffer;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $phpUnitReturnCode = 0;
        $scrutinizerReturnCode = 0;

        $scrutinizerOutputFile = BASE_DIR . '/build/artifacts/scrutinizer.txt';

        $phpUnitCommand = 'php ' . PACKAGE_BASE_DIR . '/bin/phpunit.phar -c ' . BASE_DIR . '/phpunit.xml';
        $scrutinizerCommand = 'php ' . PACKAGE_BASE_DIR . '/bin/scrutinizer.phar run ' .
            '--output-file=' . $scrutinizerOutputFile . ' ' . BASE_DIR;

        $phpUnitProcess = new Process($phpUnitCommand);
        $scrutinizerProcess = new Process($scrutinizerCommand);

        if ($input->getOption('only-phpunit')) {
            $phpUnitProcess->run(array($this, 'outputBuffer'));
        } elseif ($input->getOption('only-scrutinizer')) {
            $output->writeln("\nRunning Scrutinizer...\n");
            $scrutinizerProcess->run(array($this, 'outputBuffer'));
        } else {
            $phpUnitProcess->run(array($this, 'outputBuffer'));

            $output->writeln("\nRunning Scrutinizer...\n");
            $scrutinizerProcess->run(array($this, 'outputBuffer'));
        }

        // if scrutinizer ran, show its output
        if (!$input->getOption('only-phpunit')) {
            $result = file_get_contents($scrutinizerOutputFile);
            echo $result;

            // brittle hack: if there were more than one comments, we set the the exit code to 1
            if (false === strpos($result, 'Comments: 0')) {
                $scrutinizerReturnCode = 1;
            }
        }

        if (!$input->getOption('only-phpunit')) {
            $phpUnitReturnCode = $phpUnitProcess->getExitCode();
        }

        exit((int) ($phpUnitReturnCode || $scrutinizerReturnCode));
    }
}
