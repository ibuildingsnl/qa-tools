<?php
/**
 * @author Matthijs van den Bos <matthijs@vandenbos.org>
 * @copyright 2013 Matthijs van den Bos
 */

namespace Ibuildings\QA\Tools\PHP\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('install')
            ->setDescription('Setup for Ibuildings QA Tools for PHP')
            ->setHelp('Installs all tools and config files');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var $dialog DialogHelper */
        $dialog = $this->getHelperSet()->get('dialog');

        $settings = array();
        // set some defaults
        $settings['buildArtifactsPath'] = 'build/artifacts';
        $settings['enablePhpCsFixer'] = false;
        $settings['enablePhpMessDetector'] = false;
        $settings['enablePhpAnalyzer'] = false;
        $settings['enablePhpCodeSniffer'] = false;



        $output->writeln("Starting setup of Ibuildings QA Tools for PHP");

        if (!$dialog->askConfirmation($output, "Do you want to continue? [Y/n] ", true)) {
            exit();
        } else {
            $settings['enablePhpCsFixer'] = $dialog->askConfirmation(
                $output,
                "Do you want to enable the PHP CS Fixer? [Y/n] ",
                true
            );

            if ($settings['enablePhpCsFixer']) {
                $settings['phpCsFixerLevel'] = $dialog->askAndValidate(
                    $output,
                    "What fixer level do you want to use? (psr0, psr1, psr2, all) [all] ",
                    function ($data) {
                        if (in_array($data, array("psr0", "psr1", "psr2", "all"))) {
                            return $data;
                        }
                        throw new \Exception("That fixer level is not supported");
                    },
                    false,
                    'all'
                );
            }

            $settings['enablePhpMessDetector'] = $dialog->askConfirmation(
                $output,
                "Do you want to enable the PHP Mess Detector? [Y/n] ",
                true
            );

            $settings['enablePhpAnalyzer'] = $dialog->askConfirmation(
                $output,
                "Do you want to enable the PHP Analyzer? [Y/n] ",
                true
            );

            $settings['enablePhpCodeSniffer'] = $dialog->askConfirmation(
                $output,
                "Do you want to enable the PHP Code Sniffer? [Y/n] ",
                true
            );

            if ($settings['enablePhpCodeSniffer']) {
                $settings['phpCodeSnifferCodingStyle'] = $dialog->askAndValidate(
                    $output,
                    "Wich coding standard do you want to use? (PEAR, PHPCS, PSR1, PSR2, Squiz, Zend) [PSR2] ",
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

            $settings['enablePhpSecurityChecker'] = $dialog->askConfirmation(
                $output,
                "Do you want to enable the Sensiolabs Security Checker? [Y/n] ",
                true
            );

            if ($settings['enablePhpCsFixer']
                || $settings['enablePhpMessDetector']
                || $settings['enablePhpAnalyzer']
                || $settings['enablePhpCodeSniffer']
            ) {
                $settings['srcPath'] = $dialog->askAndValidate(
                    $output,
                    "What is the path to the PHP source code? [src] ",
                    function ($data) {
                        if (file_exists(BASE_DIR . '/' . $data)) {
                            return $data;
                        }
                        throw new \Exception("That path doesn't exist");
                    },
                    false,
                    'src'
                );
            }

            $settings['enablePhpUnit'] = $dialog->askConfirmation(
                $output,
                "Do you want to enable PHPunit tests? [Y/n] ",
                true
            );

            if ($settings['enablePhpUnit']) {
                $settings['testsPath'] = $dialog->askAndValidate(
                    $output,
                    "What is the path to the PHPUnit tests? [tests] ",
                    function ($data) {
                        if (file_exists(BASE_DIR . '/' . $data)) {
                            return $data;
                        }
                        throw new \Exception("That path doesn't exist");
                    },
                    false,
                    'tests'
                );

                $settings['enablePhpUnitAutoload'] = $dialog->askConfirmation(
                    $output,
                    "Do you want to enable an autoload script for PHPUnit? [Y/n] ",
                    true
                );

                if ($settings['enablePhpUnitAutoload']) {
                    $settings['testsAutoloadPath'] = $dialog->askAndValidate(
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

            $loader = new \Twig_Loader_Filesystem(PACKAGE_BASE_DIR . '/config-dist');
            $twig = new \Twig_Environment($loader);
            $filter = new \Twig_SimpleFilter(
                'bool',
                function ($value) {
                    if ($value) {
                        return 'true';
                    } else {
                        return 'false';
                    }
                }
            );
            $twig->addFilter($filter);

            if ($settings['enablePhpUnit']) {
                $fh = fopen(BASE_DIR . '/phpunit.xml', 'w');
                fwrite(
                    $fh,
                    $twig->render(
                        'phpunit.xml.dist',
                        $settings
                    )
                );
                fclose($fh);
                $output->writeln("Config file for PHPUnit written");
            }

            if ($settings['enablePhpCsFixer']
                || $settings['enablePhpMessDetector']
                || $settings['enablePhpAnalyzer']
                || $settings['enablePhpCodeSniffer']
            ) {
                $fh = fopen(BASE_DIR . '/.scrutinizer.yml', 'w');
                fwrite(
                    $fh,
                    $twig->render(
                        '.scrutinizer.yml.dist',
                        $settings
                    )
                );
                fclose($fh);
                $output->writeln("Config file for Scrutinizer written");
            }

            @mkdir(BASE_DIR . $settings['buildArtifactsPath'], 0755, true);
            @mkdir(BASE_DIR . $settings['buildArtifactsPath'] . '/coverage-report', 0755, true);
            $output->writeln("Build artifacts directory created");
        }
    }
}
