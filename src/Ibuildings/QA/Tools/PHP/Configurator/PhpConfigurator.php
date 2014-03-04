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

use Ibuildings\QA\Tools\Common\Configurator\ConfiguratorInterface;
use Ibuildings\QA\Tools\Common\Settings;

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Can configure Php source paths
 *
 * Class PhpConfigurator
 * @package Ibuildings\QA\Tools\Php\Configurator
 */
class PhpConfigurator
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

        $this->settings['enablePhpTools'] = false;
    }

    /**
     * Asks user what the path to Php source is.
     */
    public function configure()
    {
        $this->settings['enablePhpTools'] = $this->dialog->askConfirmation(
            $this->output,
            "\n<comment>Do you want to install the QA tools for PHP? [Y/n] </comment>",
            true
        );

        if ($this->settings['enablePhpTools']) {
            $this->output->writeln("\n<info>Configuring PHP inspections</info>\n");
        }
    }
}
