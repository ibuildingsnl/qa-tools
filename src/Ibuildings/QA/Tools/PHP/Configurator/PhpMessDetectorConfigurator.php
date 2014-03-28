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
use Ibuildings\QA\Tools\Common\Configurator\Helper\MultiplePathHelper;
use Ibuildings\QA\Tools\Common\Settings;

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Can configure setting for PHP Mess Detector
 *
 * Class PhpMessDetectorConfigurator
 *
 * @package Ibuildings\QA\Tools\PHP\Configurator
 */
class PhpMessDetectorConfigurator extends AbstractWritableConfigurator
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
     * @var Settings
     */
    protected $settings;

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

        $this->settings['enablePhpMessDetector'] = false;
    }

    public function configure()
    {
        if (!$this->settings['enablePhpTools']) {
            return false;
        }

        $default = (empty($this->settings['enablePhpMessDetector'])) ? true : $this->settings['enablePhpMessDetector'];
        $this->settings['enablePhpMessDetector'] = $this->dialog->askConfirmation(
            $this->output,
            "Do you want to enable the PHP Mess Detector?",
            $default
        );


        // Exclude default patterns
        $default = (!empty($this->settings['phpMdExcludePatterns']))
            ? implode(',', $this->settings['phpMdExcludePatterns'])
            : '';
        $excludePatterns = $this->multiplePathHelper->askPatterns(
            "  - Which patterns should be excluded for PHP Mess Detector?",
            $default,
            "  - Do you want to exclude custom patterns for PHP Mess Detector?",
            isset($this->settings['phpMdExcludePatterns'])||false
        );

        $this->settings['phpMdExcludePatterns'] = $excludePatterns;
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public function writeConfig()
    {
        if ($this->shouldWrite()) {
            $fh = fopen($this->settings->getBaseDir() . '/phpmd.xml', 'w');
            fwrite(
                $fh,
                $this->getConfigContent('phpmd.xml.dist', $this->settings->getArrayCopy())
            );
            fclose($fh);
            $this->output->writeln("\n<info>Config file for PHP Mess Detector written</info>");
        }
    }

    /**
     * @inheritdoc
     */
    protected function shouldWrite()
    {
        return $this->settings['enablePhpMessDetector'] === true;
    }
}
