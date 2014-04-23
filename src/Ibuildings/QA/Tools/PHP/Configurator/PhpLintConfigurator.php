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

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Can configure setting for PHPLint
 *
 * Class PhpLintConfigurator
 * @package Ibuildings\QA\Tools\PHP\Configurator
 */
class PhpLintConfigurator implements ConfiguratorInterface
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
     * @var \Ibuildings\QA\Tools\Common\Settings
     */
    protected $settings;

    /**
     * @param OutputInterface $output
     * @param DialogHelper $dialog
     * @param Settings $settings
     */
    public function __construct(
        OutputInterface $output,
        DialogHelper $dialog,
        Settings $settings
    ) {
        $this->output = $output;
        $this->dialog = $dialog;
        $this->settings = $settings;

        $this->settings['enablePhpLint'] = false;
    }

    /**
     *
     */
    public function configure()
    {
        if (!$this->settings['enablePhpTools']) {
            return false;
        }

        $default = (empty($this->settings['enablePhpLint'])) ? true : $this->settings['enablePhpLint'];
        $this->settings['enablePhpLint'] = $this->dialog->askConfirmation(
            $this->output,
            "Do you want to enable PHP Lint?",
            $default
        );
    }
}
