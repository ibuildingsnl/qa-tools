<?php
/**
 * @author Matthijs van den Bos <matthijs@vandenbos.org>
 * @copyright 2013 Matthijs van den Bos
 */

namespace Ibuildings\QA\Tools\Common\Console;

use Ibuildings\QA\Tools\Common\CommandExistenceChecker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InstallCommand
 * @package Ibuildings\QA\Tools\Common\Console
 *
 * @SuppressWarnings(PHPMD)
 */
class InstallPreCommitHookCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('install:pre-commit')
            ->setDescription('Sets up the pre-commit hook for the Ibuildings QA Tools')
            ->setHelp('Sets up the pre-commit hook for the Ibuildings QA Tools');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Starting setup of the pre-commit hook for the Ibuildings QA Tools<info>");

        // Test if correct ant version is installed
        $commandExistenceChecker = new CommandExistenceChecker();
        if (!$commandExistenceChecker->commandExists('ant -version', $message, InstallCommand::MINIMAL_VERSION_ANT)) {
            $output->writeln("\n<error>{$message} -> Exiting.</error>");
            return;
        }

        $this->configurePreCommitHook($input, $output);
        $this->writePreCommitHook($input, $output);
    }

    protected function configurePreCommitHook(InputInterface $input, OutputInterface $output)
    {
        $this->settings['enablePreCommitHook'] = $this->dialog->askConfirmation(
            $output,
            "\n<comment>Do you want to enable the git pre-commit hook? It will run the QA tools on every commit [Y/n] </comment>",
            true
        );

        if (!$this->settings['enablePreCommitHook']) {
            return;
        }

        // Test if correct ant version is installed
        $commandExistenceChecker = new CommandExistenceChecker();
        if (!$commandExistenceChecker->commandExists(array('md5', 'md5sum'), $message, null, $foundCommand)) {
            $output->writeln("\n<error>{$message} -> Exiting.</error>");
            return;
        }
        $this->settings['md5Command'] = $foundCommand;

        // Test if correct and version is installed
        $commandExistenceChecker = new CommandExistenceChecker();
        if (!$commandExistenceChecker->commandExists('git', $message, InstallCommand::MINIMAL_VERSION_ANT)) {
            $output->writeln("\n<error>{$message} -> Exiting.</error>");
            return;
        }

        $gitHooksDirExists = is_dir($this->settings['baseDir'] . '/.git/hooks');
        if ($this->settings['enablePreCommitHook'] && !$gitHooksDirExists) {
            $output->writeln(
                "<error>You don't have an initialized git repo or hooks directory. Not setting pre-commit hook.</error>"
            );
            $this->settings['enablePreCommitHook'] = false;
        }

        $gitPreCommitHookExists = file_exists($this->settings['baseDir'] . '/.git/hooks/pre-commit');
        if ($gitPreCommitHookExists) {
            $output->writeln("<error>You already have a git pre-commit hook.</error>");
            $overwritePreCommitHook = $this->dialog->askConfirmation(
                $output,
                "  - Do you want to overwrite your current pre-commit hook? [y/N] ",
                false
            );
            if (!$overwritePreCommitHook) {
                $this->settings['enablePreCommitHook'] = false;
            }
        }
    }

    protected function writePreCommitHook(InputInterface $input, OutputInterface $output)
    {
        if ($this->settings['enablePreCommitHook']) {
            $fh = fopen($this->settings['baseDir'] . '/.git/hooks/pre-commit', 'w');
            fwrite(
                $fh,
                $this->twig->render(
                    'pre-commit.dist',
                    $this->settings->getArrayCopy()
                )
            );
            fclose($fh);
            chmod($this->settings['baseDir'] . '/.git/hooks/pre-commit', 0755);
            $output->writeln("\n<info>Commit hook written</info>");
        }
    }
}
