<?php

/**
 * This file is part of Ibuildings QA-Tools.
 *
 * (c) Ibuildings
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ibuildings\QA\Tools\Common\Configurator;

use Ibuildings\QA\Tools\Common\Settings;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BuildArtifactsConfigurator
 * @package Ibuildings\QA\Tools\Common\Configurator
 */
class BuildArtifactsConfigurator implements ConfiguratorInterface
{
    /**
     * default artifact path
     */
    const DEFAULT_ARTIFACT_PATH = 'build/artifacts';

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $output;

    /**
     * @var \Symfony\Component\Console\Helper\DialogHelper
     */
    private $dialog;

    /**
     * @var \Ibuildings\QA\Tools\Common\Settings
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
    ) {
        $this->output = $output;
        $this->dialog = $dialog;
        $this->settings = $settings;
    }

    public function configure()
    {
        $this->settings['buildArtifacts'] = array();
        $this->settings['buildArtifacts']['enabled'] = $this->confirmBuildArtifactGeneration();

        if (!$this->settings['buildArtifacts']['enabled']) {
            return;
        }

        $this->settings['buildArtifacts']['path'] = $this->askArtifactWritePath();
    }

    /**
     * Ensures that the user enters a valid directory or agrees that the entered directory
     * is created for them
     *
     * @param string $path
     * @return string
     * @throws \Exception
     */
    public function validateArtifactWritePath($path)
    {
        if (is_dir($this->settings->getBaseDir() . '/' . $path)) {
            return $path;
        }

        $confirmed = $this->dialog->askConfirmation(
            $this->output,
            '  - Are you sure? The path does not exist and will be created.',
            true
        );

        if (!$confirmed) {
            throw new \Exception(sprintf('Chosen not to use path "%s", trying again..."', $path));
        }

        return $path;
    }

    /**
     * @return bool
     */
    private function confirmBuildArtifactGeneration()
    {
        $question = "By default results are shown on the CLI (recommended if you are using travis)
  - Do you want to generate build artifacts?";

        return $this->dialog->askConfirmation($this->output, $question, false);
    }

    /**
     * @return string
     */
    private function askArtifactWritePath()
    {
        $default = $this->getDefaultValue();

        return $this->dialog->askAndValidate(
            $this->output,
            sprintf('Where do you want to store the build artifacts? [%s] ', $default),
            array($this, 'validateArtifactWritePath'),
            false,
            $default
        );
    }

    /**
     * @return string
     */
    private function getDefaultValue()
    {
        if (!empty($this->settings['buildArtifacts']['path'])) {
            return $this->settings['buildArtifacts']['path'];
        }

        return self::DEFAULT_ARTIFACT_PATH;
    }
}
