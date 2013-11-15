<?php
namespace Ibuildings\QA\Tools\Javascript\Console;

use Symfony\Component\Console\Helper\DialogHelper;
use Ibuildings\QA\Tools\Common\Console\InstallCommand\Settings;

class JsHintConfigurator
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var DialogHelper
     */
    private $dialogHelper;

    /**
     * @var Settings
     */
    private $settings;

    public function __construct(
        OutputInterface $output,
        DialogHelper $dialogHelper,
        Settings $settings
    )
    {
        $this->output = $output;
        $this->dialogHelper = $dialogHelper;
        $this->settings = $settings;
    }

    public function configure()
    {
        $settings->set('enableJsHint', $dialog->askConfirmation(
            "Do you want to enable JSHint? [Y/n] ",
            true
        ));

        if (!$this->commandExists('node')) {
            $output->writeln("<error>You don't have Node.js installed. Not enabling JSHint.</error>");
            $settings->set('enableJsHint', false);
        }
    }
}