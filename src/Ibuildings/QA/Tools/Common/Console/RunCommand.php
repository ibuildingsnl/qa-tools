<?php
/**
 * @author Matthijs van den Bos <matthijs@vandenbos.org>
 * @copyright 2013 Matthijs van den Bos
 */

namespace Ibuildings\QA\Tools\Common\Console;

use Ibuildings\QA\Tools\Common\CommandExistenceChecker;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
                'The path to filter the changeset by. This option can be set multiple times',
                static::DEFAULT_BUILD_TARGET
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

        passthru("ant -e -f build-pre-commit.xml -logger org.apache.tools.ant.NoBannerLogger $target");
    }
}
