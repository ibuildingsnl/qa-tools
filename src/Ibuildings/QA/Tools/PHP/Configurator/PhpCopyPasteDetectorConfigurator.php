<?php

/**
 * This file is part of Ibuildings QA-Tools.
 *
 * (c) Ibuildings
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ibuildings\QA\Tools\PHP\Configurator;

use Ibuildings\QA\Tools\Common\Configurator\ConfiguratorInterface;
use Ibuildings\QA\Tools\Common\Settings;
use Ibuildings\QA\Tools\Common\Configurator\Helper\MultiplePathHelper;

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Can configure setting for PHP Copy Paste Detector
 *
 * Class PhpCopyPasteDetectorConfigurator
 * @package Ibuildings\QA\Tools\PHP\Configurator
 */
class PhpCopyPasteDetectorConfigurator implements ConfiguratorInterface
{
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * @var \Symfony\Component\Console\Helper\DialogHelper
     */
    protected $dialog;

    /**
     * @var \Ibuildings\QA\Tools\Common\Configurator\Helper\MultiplePathHelper
     */
    protected $multiplePathHelper;

    /**
     * @var \Ibuildings\QA\Tools\Common\Settings
     */
    protected $settings;

    /**
     * @param OutputInterface $output
     * @param DialogHelper $dialog
     * @param MultiplePathHelper $multiplePathHelper
     * @param Settings $settings
     */
    public function __construct(
        OutputInterface $output,
        DialogHelper $dialog,
        MultiplePathHelper $multiplePathHelper,
        Settings $settings
    ) {
        $this->output = $output;
        $this->dialog = $dialog;
        $this->multiplePathHelper = $multiplePathHelper;
        $this->settings = $settings;

        $this->settings['enablePhpCopyPasteDetection'] = false;
    }

    public function configure()
    {
        if (!$this->settings['enablePhpTools']) {
            return false;
        }

        $default = (isset($this->settings['enablePhpCopyPasteDetection']))
            ? $this->settings['enablePhpCopyPasteDetection']
            : false;
        $this->settings['enablePhpCopyPasteDetection'] = $this->dialog->askConfirmation(
            $this->output,
            "Do you want to enable PHP Copy Paste Detection?",
            $default
        );

        if (!$this->settings['enablePhpCopyPasteDetection']) {
            return;
        }

        // Tests is the Symfony default
        $default = (!empty($this->settings['phpCpdExcludePatterns']))
            ? implode(',', $this->settings['phpCpdExcludePatterns'])
            : 'Tests';

        $this->settings['phpCpdExcludePatterns'] = $this->multiplePathHelper->askPatterns(
            "Which patterns should be excluded for PHP Copy Paste detection?",
            $default,
            "Do you want to exclude patterns for PHP Copy Paste detection?"
        );
    }
}
