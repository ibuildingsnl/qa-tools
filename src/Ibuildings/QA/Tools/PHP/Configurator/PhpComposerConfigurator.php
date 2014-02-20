<?php

namespace Ibuildings\QA\Tools\PHP\Configurator;

use Ibuildings\QA\Tools\Common\Configurator\ConfiguratorInterface;
use Ibuildings\QA\Tools\Common\Settings;

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Can configure setting for PHP Security Checker
 *
 * Class PhpSecurityCheckerConfigurator
 * @package Ibuildings\QA\Tools\PHP\Configurator
 */
class PhpComposerConfigurator
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

        $this->settings['enableComposer'] = false;
    }

    public function configure()
    {
        if (!$this->settings['enablePhpTools']) {
            return false;
        }

        $choice = $this->dialog->select(
            $this->output,
            "Do you want to run `./composer.phar install` on every commit?",
            array('n' => 'no (default)', 'y' => 'yes', 'a' => 'ask every commit'),
            'n'
        );

        if ($choice === 'n') {
            $this->settings['enableComposer'] = false;
        } elseif ($choice === 'y') {
            $this->settings['enableComposer'] = true;
        } else {
            // $choice === 'ask', invalid values are caught by the dialoghelper
            $this->settings['enableComposer'] = 'ask';
        }
    }
}
