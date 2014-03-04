<?php

/**
 * This file is part of Ibuildings QA-Tools.
 *
 * (c) Ibuildings
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ibuildings\QA\Tools\Common\Console;

use Ibuildings\QA\Tools\Common\Settings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AbstractCommand
 *
*@package Ibuildings\QA\Tools\Common\Console
 *
 * @SuppressWarnings(PHPMD)
 */
abstract class AbstractCommand extends Command
{
    /* Minimal version of git that is required, note that version stashing untracked files was not possible */
    const MINIMAL_VERSION_GIT = '1.7.8';

    /**
     * Lowest version of ant on which QA Tools is known to work
     * This could be increased to 1.8 since that makes it possible to use variables
     * instead of property names for if/unless constructs
     * see: https://ant.apache.org/manual/properties.html#if+unless
     */
    const MINIMAL_VERSION_ANT = '1.7.1';

    /**
     * @var Settings
     */
    protected $settings;

    /** @var DialogHelper */
    protected $dialog;

    /** @var \Twig_Environment */
    protected $twig;

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->settings = $this->getApplication()->getSettings();

        $this->dialog = $this->getHelperSet()->get('dialog');

        $loader = new \Twig_Loader_Filesystem($this->settings->getPackageBaseDir() . '/config-dist');
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

        $this->parseComposerConfig();
    }

    /**
     * @throws \Exception
     * @return array
     */
    protected function parseComposerConfig()
    {
        if (!file_exists($this->settings->getBaseDir() . DIRECTORY_SEPARATOR . 'composer.json')) {
            throw new \Exception("Could not find composer.json in project root dir '" . $this->settings->getBaseDir() . "'");
        }

        $file = file_get_contents($this->settings->getBaseDir() . DIRECTORY_SEPARATOR . 'composer.json');

        $parsedFile = json_decode($file, true);

        if ($parsedFile === null) {
            throw new \Exception("Could not read composer.json. Is it valid JSON?");
        }

        $this->composerConfig = array();

        if (array_key_exists('config', $parsedFile)) {
            $this->composerConfig = $parsedFile['config'];
        }

        if (array_key_exists('bin-dir', $this->composerConfig)) {
            $this->settings['composerBinDir'] = $this->composerConfig['bin-dir'];
        } else {
            $this->settings['composerBinDir'] = 'vendor/bin';
        }
    }
}
