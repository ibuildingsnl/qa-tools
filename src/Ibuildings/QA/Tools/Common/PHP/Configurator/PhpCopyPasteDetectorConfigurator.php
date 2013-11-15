<?php

namespace Ibuildings\QA\Tools\Common\PHP\Configurator;

use Ibuildings\QA\Tools\Common\Configurator\ConfiguratorInterface;
use Ibuildings\QA\Tools\Common\Settings;

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Can configure setting for PHP copy paste detector
 *
 * Class PhpLintConfigurator
 * @package Ibuildings\QA\Tools\Common\PHP\Configurator
 */
class PhpCopyPasteDetectorConfigurator
    implements ConfiguratorInterface
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var DialogHelper
     */
    private $dialog;

    /**
     * @var Settings
     */
    private $settings;

    /**
     * @param OutputInterface $output
     * @param DialogHelper $dialog
     * @param Settings $settings
     */
    public function __construct(
        OutputInterface $output,
        DialogHelper $dialog,
        Settings $settings
    )
    {
        $this->output = $output;
        $this->dialog = $dialog;
        $this->settings = $settings;

        $this->settings['enablePhpCopyPasteDetection'] = false;
    }

    public function configure()
    {
        $this->settings['enablePhpCopyPasteDetection'] = $this->dialog->askConfirmation(
            $this->output,
            "Do you want to enable PHP Copy Paste Detection? [Y/n] ",
            true
        );
    }
}