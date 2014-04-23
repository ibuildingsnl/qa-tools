<?php

/**
 * This file is part of Ibuildings QA-Tools.
 *
 * (c) Ibuildings
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ibuildings\QA\Tools\Javascript\Console;

use Ibuildings\QA\Tools\Common\CommandExistenceChecker;
use Ibuildings\QA\Tools\Common\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InstallCommand
 * @package Ibuildings\QA\Tools\Common\Console
 */
class InstallJsHintCommand extends AbstractCommand
{
    const CODE_SUCCESS = 0;
    const CODE_ERROR = 1;

    protected function configure()
    {
        $this
            ->setName('install:jshint')
            ->setDescription('Installs JSHint and it\'s dependencies using NPM')
            ->setHelp('Installs JSHint and it\'s dependencies using NPM');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Starting setup of the pre-commit hook for the Ibuildings QA Tools<info>");

        // Test if node is installed
        $commandExistenceChecker = $this->getCommandExistenceChecker();
        if (!$commandExistenceChecker->commandExists('node', $message)) {
            $output->writeln("\n<error>{$message} -> Not enabling JSHint.</error>");

            return self::CODE_ERROR;
        }

        // Test if node package manager is installed
        if (!$commandExistenceChecker->commandExists('npm', $message)) {
            $output->writeln("\n<error>{$message} -> Not enabling JSHint.</error>");
            $this->settings['enableJsHint'] = false;

            return self::CODE_ERROR;
        }

        $returnVal = $this->installNpmDependencies();

        if (!empty($returnVal)) {
            $output->writeln("\n<error>Could not install JSHint -> Not enabling JSHint.</error>");
            $this->settings['enableJsHint'] = false;

            return self::CODE_ERROR;
        }

        return self::CODE_SUCCESS;
    }

    /**
     * Will install jshint
     *
     * @return mixed
     *
     * @codeCoverageIgnore
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function installNpmDependencies()
    {
        // Install npm dependencies (JSHint)
        exec("cd vendor/ibuildings/qa-tools && npm install && ln -sf ../node_modules/.bin/jshint bin/", $consoleOutput, $returnVal);

        return $returnVal;
    }
}
