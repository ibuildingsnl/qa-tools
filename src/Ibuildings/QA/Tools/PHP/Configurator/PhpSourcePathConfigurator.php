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
use Ibuildings\QA\Tools\Common\Configurator\Helper\MultiplePathHelper;
use Ibuildings\QA\Tools\Common\Settings;

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Can configure PHP source paths
 *
 * Class PhpSourcePathConfigurator
 * @package Ibuildings\QA\Tools\PHP\Configurator
 */
class PhpSourcePathConfigurator implements ConfiguratorInterface
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
    }

    public function configure()
    {
        if (!$this->isEnabled()) {
            return;
        }

        $default = (isset($this->settings['phpSrcPath'])) ? $this->settings['phpSrcPath'] : 'src';
        $this->settings['phpSrcPath'] = $this->multiplePathHelper->askPaths(
            "At which paths is the PHP source code located?",
            $default
        );
    }

    private function isEnabled()
    {
        return (
            $this->settings['enablePhpTools']
            &&
            ( $this->settings['enablePhpMessDetector']
                || $this->settings['enablePhpCodeSniffer']
                || $this->settings['enablePhpCopyPasteDetection']
                || $this->settings['enablePhpLint']
            )
        );
    }
}
