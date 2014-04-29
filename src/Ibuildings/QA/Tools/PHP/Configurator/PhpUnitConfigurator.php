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

use Ibuildings\QA\Tools\Common\Configurator\ConfigurationWriterInterface;
use Ibuildings\QA\Tools\Common\Settings;

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Can configure settings for PHPUnit
 *
 * Class PhpUnitConfigurator
 *
 * @package Ibuildings\QA\Tools\PHP\Configurator
 */
class PhpUnitConfigurator implements ConfigurationWriterInterface
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
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @param OutputInterface   $output
     * @param DialogHelper      $dialog
     * @param Settings          $settings
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
        if (!$this->settings['enablePhpTools']) {
            $this->settings['enablePhpUnit'] = false;
            return;
        }

        $this->output->writeln("\n<info>Configuring PHPUnit</info>\n");

        $this->settings['enablePhpUnit'] = $this->confirmEnablingPhpUnit();
        if (!$this->settings['enablePhpUnit']) {
            return;
        }

        if ($this->hasCustomPhpUnitXml($this->output, $this->settings)) {
            return;
        }

        $this->settings['phpTestsPath'] = $this->askForPathsToTests();
        $this->enablePhpUnitAutoLoad($this->output, $this->settings);
    }

    /**
     * @return bool
     */
    protected function confirmEnablingPhpUnit()
    {
        $default = isset($this->settings['enablePhpUnit']) ? $this->settings['enablePhpUnit'] : true;
        return $this->dialog->askConfirmation(
            $this->output,
            "Do you want to enable PHPUnit tests?",
            $default
        );
    }


    /**
     * Custom PHPUnit configuration?
     *
     * @param OutputInterface $output
     * @param Settings $settings
     * @return bool
     */
    protected function hasCustomPhpUnitXml(OutputInterface $output, Settings $settings)
    {
        $default = (empty($this->settings['customPhpUnitXml'])) ? false : $this->settings['customPhpUnitXml'];
        $settings['customPhpUnitXml'] = $this->dialog->askConfirmation(
            $output,
            "Do you have a custom PHPUnit config? (for example, Symfony has one in 'app/phpunit.xml.dist')",
            $default
        );

        // No need to go further.
        if (false === $settings['customPhpUnitXml']) {
            return false;
        }

        $default = (empty($settings['phpUnitConfigPath'])) ? 'app/phpunit.xml.dist' : $settings['phpUnitConfigPath'];
        $settings['phpUnitConfigPath'] = $this->dialog->askAndValidate(
            $output,
            "What is the path to the custom PHPUnit config? [{$default}] ",
            function ($data) use ($settings) {
                if (file_exists($settings->getBaseDir() . '/' . $data)) {
                    return $data;
                }
                throw new \Exception("That path doesn't exist");
            },
            false,
            $default
        );

        return true;
    }

    /**
     * @return mixed
     */
    protected function askForPathsToTests()
    {
        $default = (empty($this->settings['phpTestsPath'])) ? 'tests' : $this->settings['phpTestsPath'];
        $baseDir = $this->settings->getBaseDir();
        $validator = function ($data) use ($baseDir) {
            if (is_dir($baseDir . '/' . $data)) {
                return $data;
            }
            throw new \Exception("That path doesn't exist");
        };

        return $this->dialog->askAndValidate(
            $this->output,
            "What is the path to the PHPUnit tests? [{$default}] ",
            $validator,
            false,
            $default
        );
    }

    /**
     * @param OutputInterface $output
     * @param Settings $settings
     * @return bool
     */
    protected function enablePhpUnitAutoLoad(OutputInterface $output, Settings $settings)
    {
        $default = (empty($settings['enablePhpUnitAutoload'])) ? true : $settings['enablePhpUnitAutoload'];
        $settings['enablePhpUnitAutoload'] = $this->dialog->askConfirmation(
            $output,
            "Do you want to enable an autoload script for PHPUnit?",
            $default
        );

        if (false === $settings['enablePhpUnitAutoload']) {
            return false;
        }

        if ($settings['enablePhpUnitAutoload']) {
            $default = (empty($settings['phpUnitAutoloadPath']))
                ? 'vendor/autoload.php'
                : $settings['phpUnitAutoloadPath'];
            $settings['phpUnitAutoloadPath'] = $this->dialog->askAndValidate(
                $output,
                "What is the path to the autoload script for PHPUnit? [{$default}] ",
                function ($data) use ($settings) {
                    if (file_exists($settings->getBaseDir() . '/' . $data)) {
                        return $data;
                    }
                    throw new \Exception("That path doesn't exist");
                },
                false,
                $default
            );
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function shouldWrite()
    {
        return $this->settings['enablePhpUnit'] && !$this->settings['customPhpUnitXml'];
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public function writeConfig()
    {
        $filesystem = new Filesystem();
        try {
            $filesystem->dumpFile(
                $this->settings->getBaseDir() . '/phpunit.xml',
                $this->twig->render('phpunit.xml.dist', $this->settings->getArrayCopy())
            );
        } catch (IOException $e) {
            $this->output->writeln(sprintf(
                '<error>Could not write phpunit.xml, error: "%s"</error>',
                $e->getMessage()
            ));
            return;
        }

        $this->output->writeln("\n<info>Config file for PHPUnit written</info>");
    }
}
