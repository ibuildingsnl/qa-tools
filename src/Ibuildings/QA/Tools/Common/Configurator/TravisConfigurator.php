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
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class TravisConfigurator
 * @package Ibuildings\QA\Tools\Common\Configurator
 */
class TravisConfigurator extends AbstractWritableConfigurator
{
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
    ) {
        $this->output = $output;
        $this->dialog = $dialog;
        $this->settings = $settings;
        $this->twig = $twig;
    }

    public function configure()
    {
        $this->settings['travis'] = array();
        if (!$this->confirmTravisUsage()) {
            $this->settings['travis']['enabled'] = false;
            return;
        }

        $this->settings['travis']['enabled'] = true;
        $this->settings['travis']['phpVersions'] = $this->askPhpVersions();
        $this->settings['travis']['slack']['enabled'] = $this->confirmSlackNotifierEnabled();

        if ($this->settings['travis']['slack']['enabled']) {
            $this->settings['travis']['slack']['token'] = $this->askSlackToken();
        }
    }

    public function writeConfig()
    {
        // not sure if shouldWrite is called, this can be removed if so
        if (!$this->shouldWrite()) {
            return;
        }

        $filesystem = new Filesystem();

        try {
            $filesystem->dumpFile(
                $this->settings->getBaseDir() . '/.travis.yml',
                $this->twig->render('.travis.yml.dist', $this->settings['travis'])
            );

            $filesystem->dumpFile(
                $this->settings->getBaseDir() . '/.travis.php.ini',
                $this->twig->render('.travis.php.ini.dist')
            );
        } catch (IOException $e) {
            $this->output->writeln(sprintf(
                '<error>Could not write a config file in Travis Configurator, error: "%s"</error>',
                $e->getMessage()
            ));
            return;
        }

        $this->output->writeln("\n<info>Files for Travis integration have been written</info>");
    }

    public function shouldWrite()
    {
        return $this->settings['travis']['enabled'];
    }

    /**
     * @return bool
     */
    private function confirmTravisUsage()
    {
        return $this->dialog->askConfirmation(
            $this->output,
            "Do you want to enable Travis integration for this project?"
        );
    }

    /**
     * @return array
     */
    private function askPhpVersions()
    {
        // see http://docs.travis-ci.com/user/ci-environment/#PHP-versions
        $availableVersions = array(1 => '5.6', 2 => '5.5', 3 => '5.4', 4 => '5.3', 5 => 'hhvm');

        $done = false;
        $selected = array();
        while (!$done) {
            $selected = $this->dialog->select(
                $this->output,
                "Which versions of php do you want to test this project on (enter the keys comma separated) [5.5]? ",
                $availableVersions,
                2,
                false,
                'Value "%s" is invalid',
                true
            );

            $selected = array_intersect_key($availableVersions, array_flip($selected));

            $done = $this->dialog->askConfirmation(
                $this->output,
                sprintf('You have chosen the following versions: "%s", is this correct? ', implode('", "', $selected)),
                true
            );
        }

        return $selected;
    }

    /**
     * @return bool
     */
    private function confirmSlackNotifierEnabled()
    {
        return $this->dialog->askConfirmation(
            $this->output,
            "Do you want to enable Slack Notifications from Travis for this project?"
        );
    }

    /**
     * @return string
     */
    private function askSlackToken()
    {
        $question = "Please paste your slack credentials \n"
            . "  (see http://docs.travis-ci.com/user/notifications/#Slack-notifications): \n";

        // very basic validation
        $validator = function ($token) {
            // we must have a string, that contains a : not at the starting position
            // token format is username:hashedtoken
            if (is_string($token) && strpos($token, ':')) {
                return $token;
            }

            throw new \Exception("Please enter a valid token");
        };

        return $this->dialog->askAndValidate($this->output, $question, $validator);
    }
}
