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
 * Can configure settings for PHP Code Sniffer
 *
 * Class PhpCodeSnifferConfigurator
 *
 * @package Ibuildings\QA\Tools\PHP\Configurator
 */
class PhpCodeSnifferConfigurator extends AbstractWritableConfigurator
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
     * @var MultiplePathHelper
     */
    protected $multiplePathHelper;
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

        $this->settings['enablePhpCodeSniffer'] = false;
    }

    /**
     * Asks in the commandline if the user want to enable the codesniffer for the given project
     *
     */
    protected function askEnable()
    {
        $default = (empty($this->settings['enablePhpCodeSniffer'])) ? false : $this->settings['enablePhpCodeSniffer'];
        $this->settings['enablePhpCodeSniffer'] = $this->dialog->askConfirmation(
            $this->output,
            "Do you want to enable the PHP Code Sniffer?",
            $default
        );

        if ($this->settings['enablePhpCodeSniffer']) {
            $default = (empty($this->settings['phpCodeSnifferCodingStyle']))
                ? 'PSR2'
                : $this->settings['phpCodeSnifferCodingStyle'];
            $this->settings['phpCodeSnifferCodingStyle'] = $this->dialog->askAndValidate(
                $this->output,
                "  - Which coding standard do you want to use? (PEAR, PHPCS, PSR1, PSR2, Squiz, Zend) [{$default}] ",
                function ($data) {
                    if (in_array($data, array("PEAR", "PHPCS", "PSR1", "PSR2", "Squiz", "Zend"))) {
                        return $data;
                    }
                    throw new \Exception("That coding style is not supported");
                },
                false,
                $default
            );
        }
    }

    /**
     * Asks if the user wants to exclude some standard symfony paths
     *
     * @return array
     */
    protected function askExcludeSymfony()
    {
        $symfonyPatterns = array();

        $default = (empty($this->settings['phpCsExcludeSymfony'])) ? false : $this->settings['phpCsExcludeSymfony'];
        $this->settings['phpCsExcludeSymfony'] = $this->dialog->askConfirmation(
            $this->output,
            "  - Do you want to exclude some default Symfony patterns for PHP Code Sniffer?",
            $default
        );

        if ($this->settings['phpCsExcludeSymfony']) {
            $symfonyPatterns = array(
                "src/*/*Bundle/Resources",
                "src/*/*Bundle/Tests",
                "src/*/Bundle/*Bundle/Resources",
                "src/*/Bundle/*Bundle/Tests"
            );
        }

        return $symfonyPatterns;
    }

    /**
     * Asks if the user wants to exclude any other paths
     *
     * @param array $symfonyPatterns
     */
    protected function askExcludePatterns(array $symfonyPatterns)
    {
        // Exclude default patterns
        $default = (empty($this->settings['phpCsExcludeCustomPatterns']))
            ? false
            : $this->settings['phpCsExcludeCustomPatterns'];
        $this->settings['phpCsExcludeCustomPatterns'] = $this->multiplePathHelper->askPatterns(
            "  - Which patterns should be excluded for PHP Code Sniffer?",
            '',
            "  - Do you want to exclude some custom patterns for PHP Code Sniffer?",
            $default
        );

        $this->settings['phpCsExcludePatterns'] = array_merge(
            $symfonyPatterns,
            $this->settings['phpCsExcludeCustomPatterns']
        );
    }

    /**
     * @return bool
     */
    public function configure()
    {
        if (!$this->settings['enablePhpTools']) {
            return false;
        }

        $this->askEnable();

        // Exclude symfony patterns
        $symfonyPatterns = $this->askExcludeSymfony();

        $this->askExcludePatterns(($symfonyPatterns));
    }

    /**
     * @inheritdoc
     */
    public function writeConfig()
    {
        if ($this->shouldWrite()) {
            $fh = fopen($this->settings->getBaseDir() . '/phpcs.xml', 'w');
            fwrite(
                $fh,
                $this->getConfigContent('phpcs.xml.dist', $this->settings->getArrayCopy())
            );
            fclose($fh);
            $this->output->writeln("\n<info>Config file for PHP Code Sniffer written</info>");
        }
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    protected function shouldWrite()
    {
        return $this->settings['enablePhpCodeSniffer'] === true;
    }
}
