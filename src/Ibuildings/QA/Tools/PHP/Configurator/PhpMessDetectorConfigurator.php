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
use Ibuildings\QA\Tools\Common\Configurator\Helper\MultiplePathHelper;
use Ibuildings\QA\Tools\Common\Settings;

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Can configure setting for PHP Mess Detector
 *
 * Class PhpMessDetectorConfigurator
 *
 * @package Ibuildings\QA\Tools\PHP\Configurator
 */
class PhpMessDetectorConfigurator implements ConfigurationWriterInterface
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
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @param OutputInterface    $output
     * @param DialogHelper       $dialog
     * @param MultiplePathHelper $multiplePathHelper
     * @param Settings           $settings
     * @param \Twig_Environment  $twig
     */
    public function __construct(
        OutputInterface $output,
        DialogHelper $dialog,
        MultiplePathHelper $multiplePathHelper,
        Settings $settings,
        \Twig_Environment $twig
    ) {
        $this->output = $output;
        $this->dialog = $dialog;
        $this->multiplePathHelper = $multiplePathHelper;
        $this->settings = $settings;
        $this->twig = $twig;
    }

    public function configure()
    {
        if (!$this->settings['enablePhpTools']) {
            $this->settings['enablePhpMessDetector'] = false;
            return false;
        }

        $default = $this->settings->getDefaultValueFor('enablePhpMessDetector', true);
        $this->settings['enablePhpMessDetector'] = $this->dialog->askConfirmation(
            $this->output,
            "Do you want to enable the PHP Mess Detector?",
            $default
        );

        // Exclude default patterns
        $default = $this->settings->getDefaultValueFor('phpMdExcludePatterns', array());
        $excludePatterns = $this->multiplePathHelper->askPatterns(
            "  - Which patterns should be excluded for PHP Mess Detector?",
            implode(',', $default),
            "  - Do you want to exclude custom patterns for PHP Mess Detector?",
            !empty($default)
        );

        $this->settings['phpMdExcludePatterns'] = $excludePatterns;
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
                $this->settings->getBaseDir() . '/phpmd.xml',
                $this->twig->render('phpmd.xml.dist', $this->settings->getArrayCopy())
            );

            $filesystem->dumpFile(
                $this->settings->getBaseDir() . '/phpmd-pre-commit.xml',
                $this->twig->render('phpmd-pre-commit.xml.dist', $this->settings->getArrayCopy())
            );
        } catch (IOException $e) {
            $this->output->writeln(sprintf(
                '<error>Could not write phpmd.xml, error: "%s"</error>',
                $e->getMessage()
            ));
            return;
        }

        $this->output->writeln("\n<info>Config file for PHP Mess Detector written</info>");
    }

    /**
     * @inheritdoc
     */
    public function shouldWrite()
    {
        return $this->settings['enablePhpMessDetector'] === true;
    }
}
