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
class InstallPrePushHookCommand extends Command
{
    protected $settings = array();

    /** @var DialogHelper */
    protected $dialog;

    /** @var \Twig_Environment */
    protected $twig;

    protected function configure()
    {
        $this
            ->setName('install:pre-push')
            ->setDescription('Sets up the pre-push hook for the Ibuildings QA Tools')
            ->setHelp('Sets up the pre-push hook for the Ibuildings QA Tools');
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

        $this->settings['dirname'] = basename(BASE_DIR);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Starting setup of the pre-push hook for the Ibuildings QA Tools<info>");

        if (!$this->commandExists('ant')) {
            $output->writeln("\n<error>You don't have Apache Ant installed. Exiting.</error>");
            return;
        }

        $this->configurePrePushHook($input, $output);
        $this->writePrePushHook($input, $output);
    }

    private function commandExists($cmd)
    {
        $returnVal = shell_exec("command -v $cmd");
        return (empty($returnVal) ? false : true);
    }

    protected function configurePrePushHook(InputInterface $input, OutputInterface $output)
    {
        $this->settings['enablePrePushHook'] = $this->dialog->askConfirmation(
            $output,
            "\n<comment>Do you want to enable the git pre-push hook? It will run the QA tools on every push [Y/n] </comment>",
            true
        );

        if (!$this->settings['enablePrePushHook']) {
            return;
        }

        $output->writeln(
            "<info>Please specify a path where the pre-push build sources can be extracted to. ".
            "\nIt is best not to use a temporary path so that build caches can be retained. ".
            "\nThis reduces subsequent build times greatly. ".
            "\n\nNote: a path NOT starting with '/' is treated as relative to '".BASE_DIR."'.</info>"
        );
        $this->settings['prePushBuildPath'] = $this->dialog->askAndValidate(
            $output,
            "Specify a pre-push build path [". getenv('TMPDIR') ."] ",
            function ($data) use ($output) {
                if (!file_exists(BASE_DIR . '/' . $data)) {
                    if ($this->dialog->askConfirmation(
                        $output,
                        "  - Are you sure? The path doesn't exist and will be created. [Y/n] ",
                        true
                    )) {
                        return $data;
                    }
                    throw new \Exception("Not using path '" . $this->settings['prePushBuildPath'] . " ', trying again...");
                }
                return $data;
            },
            false,
            getenv('TMPDIR')
        );

        $gitHooksDirExists = is_dir(BASE_DIR . '/.git/hooks');
        if ($this->settings['enablePrePushHook'] && !$gitHooksDirExists) {
            $output->writeln(
                "<error>You don't have an initialized git repo or hooks directory. Not setting pre-push hook.</error>"
            );
            $this->settings['enablePrePushHook'] = false;
        }

        $gitPrePushHookExists = file_exists(BASE_DIR . '/.git/hooks/pre-push');
        if ($gitPrePushHookExists) {
            $output->writeln("<error>You already have a git pre-push hook.</error>");
            $overwritePrePushHook = $this->dialog->askConfirmation(
                $output,
                "  - Do you want to overwrite your current pre-push hook? [y/N] ",
                false
            );
            if (!$overwritePrePushHook) {
                $this->settings['enablePrePushHook'] = false;
            }
        }
    }

    protected function writePrePushHook(InputInterface $input, OutputInterface $output)
    {
        if ($this->settings['enablePrePushHook']) {
            $fh = fopen(BASE_DIR . '/.git/hooks/pre-push', 'w');
            fwrite(
                $fh,
                $this->twig->render(
                    'pre-push.dist',
                    $this->settings
                )
            );
            fclose($fh);
            chmod(BASE_DIR . '/.git/hooks/pre-push', 0755);
            $output->writeln("\n<info>Push hook written</info>");
        }
    }
}
