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
class PhpUnitConfigurator
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

        $this->settings['enablePhpUnit'] = false;
        $this->settings['customPhpUnitXml'] = false;
        $this->settings['phpUnitConfigPath'] = '${basedir}';
    }

    public function configure()
    {
        $output = $this->output;
        $output->writeln("\n<info>Configuring PHPUnit</info>\n");
        $this->settings['enablePhpUnit'] = $this->dialog->askConfirmation(
            $output,
            "Do you want to enable PHPunit tests? [Y/n] ",
            true
        );

        $this->settings['customPhpUnitXml'] = $this->dialog->askConfirmation(
            $output,
            "Do you have a custom PHPUnit config? (for example, Symfony has one in 'app/phpunit.xml.dist') [y/N] ",
            false
        );

        if ($this->settings['customPhpUnitXml']) {
            $this->settings['phpUnitConfigPath'] = $this->dialog->askAndValidate(
                $output,
                "What is the path to the custom PHPUnit config? [app/phpunit.xml.dist] ",
                function ($data) {
                    if (file_exists(BASE_DIR . '/' . $data)) {
                        return $data;
                    }
                    throw new \Exception("That path doesn't exist");
                },
                false,
                'app/phpunit.xml.dist'
            );
        } else {
            if ($this->settings['enablePhpUnit']) {
                $this->settings['phpTestsPath'] = $this->dialog->askAndValidate(
                    $output,
                    "What is the path to the PHPUnit tests? [tests] ",
                    function ($data) {
                        if (is_dir(BASE_DIR . '/' . $data)) {
                            return $data;
                        }
                        throw new \Exception("That path doesn't exist");
                    },
                    false,
                    'tests'
                );

                $this->settings['enablePhpUnitAutoload'] = $this->dialog->askConfirmation(
                    $output,
                    "Do you want to enable an autoload script for PHPUnit? [Y/n] ",
                    true
                );

                if ($this->settings['enablePhpUnitAutoload']) {
                    $this->settings['phpTestsAutoloadPath'] = $this->dialog->askAndValidate(
                        $output,
                        "What is the path to the autoload script for PHPUnit? [vendor/autoload.php] ",
                        function ($data) {
                            if (file_exists(BASE_DIR . '/' . $data)) {
                                return $data;
                            }
                            throw new \Exception("That path doesn't exist");
                        },
                        false,
                        'vendor/autoload.php'
                    );
                }
            }
        }
    }
}