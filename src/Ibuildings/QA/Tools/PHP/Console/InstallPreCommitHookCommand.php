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

/**
 * Class InstallCommand
 * @package Ibuildings\QA\Tools\PHP\Console
 *
 * @SuppressWarnings(PHPMD)
 */
class InstallPreCommitHookCommand extends Command
{
    protected $settings = array();

    /** @var DialogHelper */
    protected $dialog;

    /** @var \Twig_Environment */
    protected $twig;

    protected function configure()
    {
        $this
            ->setName('install:pre-commit')
            ->setDescription('Sets up the pre-commit hook for the Ibuildings QA Tools')
            ->setHelp('Sets up the pre-commit hook for the Ibuildings QA Tools');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->dialog = $this->getHelperSet()->get('dialog');

        $loader = new \Twig_Loader_Filesystem(PACKAGE_BASE_DIR . '/config-dist');
        $this->twig = new \Twig_Environment($loader);
        $filter = new \Twig_SimpleFilter(
            'bool',
            function ($value) {
                if ($value) {
                    return 'true';
                } else {
                    return 'false';
                }
            }
        );
        $this->twig->addFilter($filter);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Starting setup of the pre-commit hook for the Ibuildings QA Tools<info>");

        if (!$this->commandExists('ant')) {
            $output->writeln("\n<error>You don't have Apache Ant installed. Exiting.</error>");
            return;
        }

        $this->configurePreCommitHook($input, $output);
        $this->writePreCommitHook($input, $output);
    }

    private function commandExists($cmd)
    {
        $returnVal = shell_exec("command -v $cmd");
        return (empty($returnVal) ? false : true);
    }

    protected function configurePreCommitHook(InputInterface $input, OutputInterface $output)
    {
        $this->settings['enablePreCommitHook'] = $this->dialog->askConfirmation(
            $output,
            "\n<comment>Do you want to enable the git pre-commit hook? It will run the QA tools on every commit [Y/n] </comment>",
            true
        );

        $gitHooksDirExists = is_dir(BASE_DIR . '/.git/hooks');
        if ($this->settings['enablePreCommitHook'] && !$gitHooksDirExists) {
            $output->writeln(
                "<error>You don't have an initialized git repo or hooks directory. Not setting pre-commit hook.</error>"
            );
            $this->settings['enablePreCommitHook'] = false;
        }

        $gitPreCommitHookExists = file_exists(BASE_DIR . '/.git/hooks/pre-commit');
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
            $fh = fopen(BASE_DIR . '/.git/hooks/pre-commit', 'w');
            fwrite(
                $fh,
                $this->twig->render(
                    'pre-commit.dist',
                    $this->settings
                )
            );
            fclose($fh);
            chmod(BASE_DIR . '/.git/hooks/pre-commit', 0755);
            $output->writeln("\n<info>Commit hook written</info>");
        }
    }
}
