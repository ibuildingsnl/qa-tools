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

use Ibuildings\QA\Tools\Common\Configurator\AbstractWritableConfigurator;
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
class PhpUnitConfigurator extends AbstractWritableConfigurator
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var DialogHelper
     */
    protected $dialog;

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

        $this->settings['enablePhpUnit'] = false;
        $this->settings['customPhpUnitXml'] = false;
        $this->settings['phpUnitConfigPath'] = '';
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


    public function configure()
    {
        if (!$this->settings['enablePhpTools']) {
            return false;
        }

        $output = $this->output;
        $output->writeln("\n<info>Configuring PHPUnit</info>\n");
        $default = (empty($this->settings['enablePhpUnit'])) ? true : $this->settings['enablePhpUnit'];
        $this->settings['enablePhpUnit'] = $this->dialog->askConfirmation(
            $output,
            "Do you want to enable PHPunit tests?",
            $default
        );

        if (!$this->hasCustomPhpUnitXml($output, $this->settings) &&  $this->settings['enablePhpUnit']) {
            $settings = $this->settings;
            $default = (empty($this->settings['phpTestsPath'])) ? 'tests' : $this->settings['phpTestsPath'];
            $this->settings['phpTestsPath'] = $this->dialog->askAndValidate(
                $output,
                "What is the path to the PHPUnit tests? [{$default}] ",
                function ($data) use ($settings) {
                    if (is_dir($settings->getBaseDir() . '/' . $data)) {
                        return $data;
                    }
                    throw new \Exception("That path doesn't exist");
                },
                false,
                $default
            );

            $this->enablePhpUnitAutoLoad($output, $this->settings);
        }
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
    protected function shouldWrite()
    {
        return $this->settings['enablePhpUnit'] && !$this->settings['customPhpUnitXml'];
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public function writeConfig()
    {
        if (!$this->shouldWrite()) {
            return;
        }

        $filesystem = new Filesystem();
        try {
            $filesystem->dumpFile(
                $this->settings->getBaseDir() . '/',
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
