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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class InstallCommand
 * @package Ibuildings\QA\Tools\Common\Console
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
        $commandExistenceChecker = $this->getCommandExistenceChecker();
        if (!$commandExistenceChecker->commandExists('ant -version', $message, InstallCommand::MINIMAL_VERSION_ANT)) {
            $output->writeln("\n<error>{$message} -> Exiting.</error>");
            return;
        }

        $this->configurePreCommitHook($input, $output);
        $this->writePreCommitHook($input, $output);
    }

    protected function configurePreCommitHook(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getApplication()->getDialogHelper();

        $this->settings['enablePreCommitHook'] = $dialog->askConfirmation(
            $output,
            "\nDo you want to enable the git pre-commit hook? It will run the QA tools on every commit",
            true
        );

        if (!$this->settings['enablePreCommitHook']) {
            return;
        }

        // Test if correct ant version is installed
        $commandExistenceChecker = $this->getCommandExistenceChecker();
        if (!$commandExistenceChecker->commandExists(array('md5', 'md5sum'), $message, null, $foundCommand)) {
            $output->writeln("\n<error>{$message} -> Exiting.</error>");
            return;
        }

        $this->settings['md5Command'] = $foundCommand;

        // Test if correct and version is installed
        if (!$commandExistenceChecker->commandExists('git', $message, InstallCommand::MINIMAL_VERSION_GIT)) {
            $output->writeln("\n<error>{$message} -> Exiting.</error>");
            return;
        }

        $gitHooksDirExists = $this->gitHooksDirExists($this->settings->getBaseDir());
        if ($this->settings['enablePreCommitHook'] && !$gitHooksDirExists) {
            $output->writeln(
                "<error>You don't have an initialized git repo or hooks directory. Not setting pre-commit hook.</error>"
            );
            $this->settings['enablePreCommitHook'] = false;
        }

        $gitPreCommitHookExists = $this->preCommitHookExists($this->settings->getBaseDir());
        if ($gitPreCommitHookExists) {
            $output->writeln("<error>You already have a git pre-commit hook.</error>");
            $overwritePreCommitHook = $dialog->askConfirmation(
                $output,
                "  - Do you want to overwrite your current pre-commit hook?",
                false
            );
            if (!$overwritePreCommitHook) {
                $this->settings['enablePreCommitHook'] = false;
            }
        }
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @codeCoverageIgnore
     */
    protected function writePreCommitHook(InputInterface $input, OutputInterface $output)
    {
        if (!$this->settings['enablePreCommitHook']) {
            return;
        }

        $filesystem = new Filesystem();
        try {
            $filesystem->dumpFile(
                $this->settings->getBaseDir() . '/.git/hooks/pre-commit',
                $this->twig->render('pre-commit.dist', $this->settings->getArrayCopy()),
                0755
            );
        } catch (IOException $e) {
            $output->writeln(sprintf(
                '<error>Could not write pre-commit hook, error: "%s"</error>',
                $e->getMessage()
            ));
        }

        $output->writeln("\n<info>Commit hook written</info>");
    }

    /**
     * @param string $baseDir
     *
     * @return bool
     */
    protected function preCommitHookExists($baseDir)
    {
        return file_exists($baseDir . '/.git/hooks/pre-commit');
    }

    /**
     * @param string $baseDir
     *
     * @return bool
     */
    protected function gitHooksDirExists($baseDir)
    {
        return is_dir($baseDir . '/.git/hooks');
    }
}
