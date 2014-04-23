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

use Ibuildings\QA\Tools\Common\Configurator\ConfiguratorInterface;
use Ibuildings\QA\Tools\Common\Settings;

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Can configure Javascript source paths
 *
 * Class JavascriptSourcePathConfigurator
 * @package Ibuildings\QA\Tools\Javascript\Configurator
 */
class JavascriptSourcePathConfigurator implements ConfiguratorInterface
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
    }

    /**
     * Asks user what the path to javascript source is.
     */
    public function configure()
    {
        if (!$this->settings['enableJsHint']) {
            return;
        }
        $baseDir = $this->settings->getBaseDir();
        $default = (empty($this->settings['javaScriptSrcPath'])) ? 'src' : $this->settings['javaScriptSrcPath'];
        $this->settings['javaScriptSrcPath'] = $this->dialog->askAndValidate(
            $this->output,
            "What is the path to the JavaScript source code? [{$default}] ",
            function ($data) use ($baseDir) {
                if (is_dir($baseDir . '/' . $data)) {
                    return $data;
                }
                throw new \Exception("That path doesn't exist");
            },
            false,
            $default
        );
    }
}
