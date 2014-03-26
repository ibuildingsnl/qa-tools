<?php

/**
 * This file is part of Ibuildings QA-Tools.
 *
 * (c) Ibuildings
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ibuildings\QA\Tools\Javascript\Configurator;

use Ibuildings\QA\Tools\Common\Configurator\AbstractWritableConfigurator;
use Ibuildings\QA\Tools\Common\Settings;
use Ibuildings\QA\Tools\Javascript\Console\InstallJsHintCommand;

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class JsHintConfigurator
 * @package Ibuildings\QA\Tools\Javascript\Configurator
 */
class JsHintConfigurator extends AbstractWritableConfigurator
{
    /**
     * @var InputInterface
     */
    protected $input;

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
     * @var InstallJsHintCommand
     */
    protected $installJsHintCommand;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param DialogHelper $dialog
     * @param Settings $settings
     * @param \Twig_Environment $twig,
     * @param InstallJsHintCommand $installJsHintCommand
     */
    public function __construct(
        InputInterface $input,
        OutputInterface $output,
        DialogHelper $dialog,
        Settings $settings,
        \Twig_Environment $twig,
        InstallJsHintCommand $installJsHintCommand
    )
    {
        $this->input = $input;
        $this->output = $output;
        $this->dialog = $dialog;
        $this->settings = $settings;
        $this->twig = $twig;
        $this->installJsHintCommand = $installJsHintCommand;

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

        if ($this->settings['enableJsHint'] === false) {
            return;
        }

        $statusCode = $this->installJsHintCommand->run($this->input, $this->output);
        if ($statusCode) {
            $this->settings['enableJsHint'] = false;
        }
    }

    /**
     * Writes config file

     * @codeCoverageIgnore
     */
    public function writeConfig()
    {
        if ($this->shouldWrite() === false) {
            return;
        }

        $fh = fopen($this->settings->getBaseDir() . '/.jshintrc', 'w');
        fwrite(
            $fh,
            $this->getConfigContent('.jshintrc.dist', $this->settings->getArrayCopy())
        );
        fclose($fh);
        $this->output->writeln("\n<info>Config file for JSHint written</info>");
    }

    /**
     * @inheritdoc
     */
    protected function shouldWrite()
    {
        return $this->settings['enableJsHint'] === true;
    }
}
