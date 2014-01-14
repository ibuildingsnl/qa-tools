<?php

namespace Ibuildings\QA\Tools\Javascript\Configurator;

use Ibuildings\QA\Tools\Common\Configurator\ConfiguratorInterface;
use Ibuildings\QA\Tools\Common\Settings;
use Ibuildings\QA\Tools\Common\CommandExistenceChecker;

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class JsHintConfigurator
 * @package Ibuildings\QA\Tools\Javascript\Configurator
 */
class JsHintConfigurator
    implements ConfiguratorInterface
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var DialogHelper
     */
    protected $dialog;

    /**
     * @var Settings
     */
    protected $settings;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @param OutputInterface $output
     * @param DialogHelper $dialog
     * @param Settings $settings
     * @param \Twig_Environment $twig
     */
    public function __construct(
        OutputInterface $output,
        DialogHelper $dialog,
        Settings $settings,
        \Twig_Environment $twig
    )
    {
        $this->output = $output;
        $this->dialog = $dialog;
        $this->settings = $settings;
        $this->twig = $twig;

        $this->settings['enableJsHint'] = false;
    }

    public function configure()
    {
        if (!$this->settings['enableJsTools']) {
            return;
        }

        $this->settings['enableJsHint'] = $this->dialog->askConfirmation(
            $this->output,
            "Do you want to enable JSHint? [Y/n] ",
            true
        );

        if ($this->settings['enableJsHint']) {
            $this->settings['enableJsTools'] = true;
        }

        // Test if node is installed
        $commandExistenceChecker = new CommandExistenceChecker();
        if (!$commandExistenceChecker->commandExists('node', $message)) {
            $this->output->writeln("\n<error>{$message} -> Not enabling JSHint.</error>");
            $this->settings['enableJsHint'] = false;

            return;
        }
    }

    /**
     * Writes config file
     */
    public function writeConfig()
    {
        if (!$this->settings['enableJsHint']) {
            return;
        }

        $fh = fopen($this->settings['baseDir'] . '/.jshintrc', 'w');
        fwrite(
            $fh,
            $this->twig->render(
                '.jshintrc.dist',
                $this->settings->toArray()
            )
        );
        fclose($fh);
        $this->output->writeln("\n<info>Config file for JSHint written</info>");
    }
}
