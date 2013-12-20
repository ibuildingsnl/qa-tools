<?php

namespace Ibuildings\QA\Tools\PHP\Configurator;

use Ibuildings\QA\Tools\Common\Configurator\ConfiguratorInterface;
use Ibuildings\QA\Tools\Common\DependencyInjection\Twig;
use Ibuildings\QA\Tools\Common\Settings;

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Can configure settings for PHPUnit
 *
 * Class PhpUnitConfigurator
 * @package Ibuildings\QA\Tools\PHP\Configurator
 */
class PhpUnitConfigurator
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
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @param OutputInterface $output
     * @param DialogHelper $dialog
     * @param Settings $settings
     * @param \Twig_Environment $twig
     */
    public function __construct(
        OutputInterface $output,
        DialogHelper $dialog,
        Settings $settings,
        \Twig_Environment $twig
    )
    {
        $this->output = $output;
        $this->dialog = $dialog;
        $this->settings = $settings;
        $this->twig = $twig;

        $this->settings['enablePhpUnit'] = false;
        $this->settings['customPhpUnitXml'] = false;
        $this->settings['phpUnitConfigPath'] = '';
    }

    public function configure()
    {
        if (!$this->settings['enablePhpTools']) {
            return false;
        }

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

    public function writeConfig()
    {
        if ($this->settings['enablePhpUnit'] && !$this->settings['customPhpUnitXml']) {
            $fh = fopen(BASE_DIR . '/phpunit.xml', 'w');
            fwrite(
                $fh,
                $this->twig->render(
                    'phpunit.xml.dist',
                    $this->settings->toArray()
                )
            );
            fclose($fh);
            $this->output->writeln("\n<info>Config file for PHPUnit written</info>");
        }
    }
}
