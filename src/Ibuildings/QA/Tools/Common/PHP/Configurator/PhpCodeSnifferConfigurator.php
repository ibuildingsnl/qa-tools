<?php

namespace Ibuildings\QA\Tools\Common\PHP\Configurator;

use Ibuildings\QA\Tools\Common\Configurator\ConfiguratorInterface;
use Ibuildings\QA\Tools\Common\Settings;

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Can configure setting for PHP code sniffer
 *
 * Class PhpLintConfigurator
 * @package Ibuildings\QA\Tools\Common\PHP\Configurator
 */
class PhpCodeSnifferConfigurator
    implements ConfiguratorInterface
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
     * @param OutputInterface $output
     * @param DialogHelper $dialog
     * @param Settings $settings
     */
    public function __construct(
        OutputInterface $output,
        DialogHelper $dialog,
        Settings $settings
    )
    {
        $this->output = $output;
        $this->dialog = $dialog;
        $this->settings = $settings;

        $this->settings['enablePhpCodeSniffer'] = false;
    }

    public function configure()
    {
        $this->settings['enablePhpCodeSniffer'] = $this->dialog->askConfirmation(
            $this->output,
            "Do you want to enable the PHP Code Sniffer? [Y/n] ",
            true
        );

        if ($this->settings['enablePhpCodeSniffer']) {
            $this->settings['phpCodeSnifferCodingStyle'] = $this->dialog->askAndValidate(
                $this->output,
                "  - Which coding standard do you want to use? (PEAR, PHPCS, PSR1, PSR2, Squiz, Zend) [PSR2] ",
                function ($data) {
                    if (in_array($data, array("PEAR", "PHPCS", "PSR1", "PSR2", "Squiz", "Zend"))) {
                        return $data;
                    }
                    throw new \Exception("That coding style is not supported");
                },
                false,
                'PSR2'
            );
        }
    }
}