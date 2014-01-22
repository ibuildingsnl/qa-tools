<?php
namespace Ibuildings\QA\Tools\PHP\Configurator;

use Ibuildings\QA\Tools\Common\Configurator\ConfiguratorInterface;
use Ibuildings\QA\Tools\Common\Settings;
use Ibuildings\QA\Tools\Common\Console\Helper\DialogInterface;
use Ibuildings\QA\Tools\Common\Console\Helper\DialogHelper;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Can configure Php source paths
 *
 * Class PhpConfigurator
 * @package Ibuildings\QA\Tools\Php\Configurator
 */
class PhpConfigurator
    implements ConfiguratorInterface, DialogInterface
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
     * @param Settings $settings
     */
    public function __construct(
        OutputInterface $output,
        Settings $settings
    )
    {
        $this->output = $output;
        $this->settings = $settings;

    }

    /**
     * Asks user what the path to Php source is.
     */
    public function configure()
    {
        $value = $this->settings['enablePhpTools'] || TRUE;

        $this->settings['enablePhpTools'] = $this->dialog->askConfirmation(
            $this->output,
            "\nDo you want to install the QA tools for PHP?",
            $value
        );

        if ($this->settings['enablePhpTools']) {
            $this->output->writeln("\n<info>Configuring PHP inspections</info>\n");
        }
    }

    /**
     * @see DialogInterface
     *
     * @param DialogHelper $helper
     */
    public function setDialogHelper(DialogHelper $helper)
    {
        $this->dialog = $helper;
    }
}