<?php

/**
 * This file is part of Ibuildings QA-Tools.
 *
 * (c) Ibuildings
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ibuildings\QA\Tools\Common\Console;

use Ibuildings\QA\Tools\Common\CommandExistenceChecker;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RunCommand
 * @package Ibuildings\QA\Tools\Common\Console
 *
 * @SuppressWarnings(PHPMD)
 */
class RunCommand extends AbstractCommand
{

    const DEFAULT_BUILD_TARGET = 'build';

    protected function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Runs the Ibuildings QA Tools on the current changeset')
            ->setHelp('Runs the Ibuildings QA Tools on the current changeset')
            ->addArgument(
                'target',
                InputArgument::OPTIONAL,
                'The tool to run',
                static::DEFAULT_BUILD_TARGET
            )
            ->addOption(
                'working-dir',
                'w',
                InputOption::VALUE_OPTIONAL,
                'The working directory context in which to execute the individual tools',
                ''
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Running the Ibuildings QA Tools<info>");

        // Test if correct ant version is installed
        $commandExistenceChecker = new CommandExistenceChecker();
        if (!$commandExistenceChecker->commandExists('ant -version', $message, static::MINIMAL_VERSION_ANT)) {
            $output->writeln("\n<error>{$message} -> Exiting.</error>");
            return;
        }

        $target = $input->getArgument('target');

        $dirOption = '';
        if ($input->getOption('working-dir')) {
            $dirOption = '-Dworking-dir=' . $input->getOption('working-dir');
        }

        $verbose = '';
        if (OutputInterface::VERBOSITY_VERBOSE === $output->getVerbosity()) {
            $verbose = '-verbose ';
        }

        passthru(
            "ant $verbose -e -f build-pre-commit.xml -logger org.apache.tools.ant.NoBannerLogger $target $dirOption",
            $exitCode
        );

        exit($exitCode);
    }
}
