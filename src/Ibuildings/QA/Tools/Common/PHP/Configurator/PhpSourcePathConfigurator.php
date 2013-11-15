<?php

namespace Ibuildings\QA\Tools\Common\PHP\Configurator;

use Ibuildings\QA\Tools\Common\Configurator\ConfiguratorInterface;
use Ibuildings\QA\Tools\Common\Settings;

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Can configure setting for PHP mess detector
 *
 * Class PhpLintConfigurator
 * @package Ibuildings\QA\Tools\Common\PHP\Configurator
 */
class PhpSourcePathConfigurator
    implements ConfiguratorInterface
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var DialogHelper
     */
    private $dialog;

    /**
     * @var Settings
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
    )
    {
        $this->output = $output;
        $this->dialog = $dialog;
        $this->settings = $settings;
    }

    public function configure()
    {
        if ($this->settings['enablePhpMessDetector']
            || $this->settings['enablePhpCodeSniffer']
            || $this->settings['enablePhpCopyPasteDetection']
        ) {
            $this->settings['phpSrcPath'] = $this->dialog->askAndValidate(
                $this->output,
                "What is the path to the PHP source code? [src] ",
                function ($data) {
                    if (is_dir(BASE_DIR . '/' . $data)) {
                        return $data;
                    }
                    throw new \Exception("That path doesn't exist");
                },
                false,
                'src'
            );
        }
    }
}