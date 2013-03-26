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

/**
 * Class InstallCommand
 * @package Ibuildings\QA\Tools\PHP\Console
 *
 * @SuppressWarnings(PHPMD)
 */
class InstallCommand extends Command
{
    protected $settings = array();

    /** @var DialogHelper */
    protected $dialog;

    /** @var \Twig_Environment */
    protected $twig;

    protected function configure()
    {
        $this
            ->setName('install')
            ->setDescription('Setup for Ibuildings QA Tools')
            ->setHelp('Installs all tools and config files');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->enableDefaultSettings();

        $this->dialog = $this->getHelperSet()->get('dialog');

        $loader = new \Twig_Loader_Filesystem(PACKAGE_BASE_DIR . '/config-dist');
        $this->twig = new \Twig_Environment($loader);
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
        $this->twig->addFilter($filter);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Starting setup of Ibuildings QA Tools<info>");

        if (!$this->commandExists('ant')) {
            $output->writeln("\n<error>You don't have Apache Ant installed. Exiting.</error>");
            return;
        }

        if (!$this->dialog->askConfirmation(
            $output,
            "\n<comment>If you already have a build config, it will be overwritten. Do you want to continue? [Y/n] </comment>",
            true
        )) {
            return;
        }

        $output->writeln("\n");
        $this->configureProjectName($input, $output);
        $this->configureBuildArtifactsPath($input, $output);

        if ($this->dialog->askConfirmation(
            $output,
            "\n<comment>Do you want to install the QA tools for PHP? [Y/n] </comment>",
            true
        )) {
            $output->writeln("\n<info>Configuring PHP inspections</info>\n");

            $this->configurePhpLint($input, $output);
            $this->configurePhpMessDetector($input, $output);
            $this->configurePhpCodeSniffer($input, $output);
            $this->configurePhpCopyPasteDetection($input, $output);
            $this->configurePhpSecurityChecker($input, $output);

            $this->configurePhpSrcPath($input, $output);

            $this->configurePhpUnit($input, $output);

            $this->writePhpUnitXml($input, $output);
            $this->writePhpCsConfig($input, $output);
        }

        if ($this->dialog->askConfirmation(
            $output,
            "\n<comment>Do you want to install the QA tools for Javascript? [Y/n] </comment>",
            true
        )) {
            $this->configureJsHint($input, $output);
            $this->configureJavaScriptSrcPath($input, $output);

            $this->writeJsHintConfig($input, $output);
        }
        $this->writeAntBuildXml($input, $output);

        $command = $this->getApplication()->find('install:pre-push');
        $command->run($input, $output);

        $command = $this->getApplication()->find('install:pre-commit');
        $command->run($input, $output);
    }

    private function enableDefaultSettings()
    {
        $this->settings['buildArtifactsPath'] = 'build/artifacts';

        $this->settings['enablePhpMessDetector'] = false;
        $this->settings['enablePhpCopyPasteDetection'] = false;
        $this->settings['enablePhpCodeSniffer'] = false;
        $this->settings['enablePhpUnit'] = false;
        $this->settings['enablePhpLint'] = false;

        $this->settings['customPhpUnitXml'] = false;
        $this->settings['phpUnitConfigPath'] = '${basedir}';

        $this->settings['enableJsHint'] = false;

        return $this;
    }

    private function commandExists($cmd) {
        $returnVal = shell_exec("command -v $cmd");
        return (empty($returnVal) ? false : true);
    }

    protected function configureBuildArtifactsPath(InputInterface $input, OutputInterface $output)
    {
        $this->settings['buildArtifactsPath'] = $this->dialog->askAndValidate(
            $output,
            "Where do you want to store the build artifacts? [".$this->settings['buildArtifactsPath']."] ",
            function ($data) use ($output) {
                if (!is_dir(BASE_DIR . '/' . $data)) {
                    if ($this->dialog->askConfirmation(
                        $output,
                        "  - Are you sure? The path doesn't exist and will be created. [Y/n] ",
                        true
                    )) {
                        return $data;
                    }
                    throw new \Exception("Not using path '" . $data . " ', trying again...");
                }
                return $data;
            },
            false,
            $this->settings['buildArtifactsPath']
        );
    }

    protected function configureProjectName(InputInterface $input, OutputInterface $output)
    {
        $dirName = basename(BASE_DIR);
        $guessedName = ucwords(str_replace(array('-', '_'), ' ', $dirName));

        $this->settings['projectName'] = $this->dialog->askAndValidate(
            $output,
            "What is the name of the project? [$guessedName] ",
            function ($data) {
                if (preg_match('/^[\w\s]+$/', $data)) {
                    return $data;
                }
                throw new \Exception("The project name may only contain 'a-zA-Z0-9_ '");
            },
            false,
            $guessedName
        );
    }


    protected function configurePhpMessDetector(InputInterface $input, OutputInterface $output)
    {
        $this->settings['enablePhpMessDetector'] = $this->dialog->askConfirmation(
            $output,
            "Do you want to enable the PHP Mess Detector? [Y/n] ",
            true
        );
    }

    protected function configurePhpCodeSniffer(InputInterface $input, OutputInterface $output)
    {
        $this->settings['enablePhpCodeSniffer'] = $this->dialog->askConfirmation(
            $output,
            "Do you want to enable the PHP Code Sniffer? [Y/n] ",
            true
        );

        if ($this->settings['enablePhpCodeSniffer']) {
            $this->settings['phpCodeSnifferCodingStyle'] = $this->dialog->askAndValidate(
                $output,
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

    protected function configurePhpCopyPasteDetection(InputInterface $input, OutputInterface $output)
    {
        $this->settings['enablePhpCopyPasteDetection'] = $this->dialog->askConfirmation(
            $output,
            "Do you want to enable PHP Copy Paste Detection? [Y/n] ",
            true
        );
    }

    protected function configurePhpSecurityChecker(InputInterface $input, OutputInterface $output)
    {
        $this->settings['enablePhpSecurityChecker'] = $this->dialog->askConfirmation(
            $output,
            "Do you want to enable the Sensiolabs Security Checker? [Y/n] ",
            true
        );
    }

    protected function configurePhpLint(InputInterface $input, OutputInterface $output)
    {
        $this->settings['enablePhpLint'] = $this->dialog->askConfirmation(
            $output,
            "Do you want to enable PHP Lint? [Y/n] ",
            true
        );
    }

    protected function configureJsHint(InputInterface $input, OutputInterface $output)
    {
        $this->settings['enableJsHint'] = $this->dialog->askConfirmation(
            $output,
            "Do you want to enable JSHint? [Y/n] ",
            true
        );

        if (!$this->commandExists('node')) {
            $output->writeln("<error>You don't have Node.js installed. Not enabling JSHint.</error>");
            $this->settings['enableJsHint'] = false;
        }
    }

    protected function configureJavaScriptSrcPath(InputInterface $input, OutputInterface $output)
    {
        if ($this->settings['enableJsHint']) {
            $this->settings['javaScriptSrcPath'] = $this->dialog->askAndValidate(
                $output,
                "What is the path to the JavaScript source code? [src] ",
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


    protected function configurePhpSrcPath(InputInterface $input, OutputInterface $output)
    {
        if ($this->settings['enablePhpMessDetector']
            || $this->settings['enablePhpCodeSniffer']
            || $this->settings['enablePhpCopyPasteDetection']
        ) {
            $this->settings['phpSrcPath'] = $this->dialog->askAndValidate(
                $output,
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

    protected function configurePhpUnit(InputInterface $input, OutputInterface $output)
    {
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

    protected function writeAntBuildXml(InputInterface $input, OutputInterface $output)
    {
        if ($this->settings['enablePhpMessDetector']
            || $this->settings['enablePhpCopyPasteDetection']
            || $this->settings['enablePhpCodeSniffer']
            || $this->settings['enablePhpUnit']
            || $this->settings['enablePhpLint']
            || $this->settings['enableJsHint']
        ) {
            $fh = fopen(BASE_DIR . '/build.xml', 'w');
            fwrite(
                $fh,
                $this->twig->render(
                    'build.xml.dist',
                    $this->settings
                )
            );
            fclose($fh);

            $this->addToGitIgnore('build');
            $this->addToGitIgnore('cache.properties');

            $output->writeln("\n<info>Ant build file written</info>");
        } else {
            $output->writeln("\n<info>No QA tools enabled. No configuration written</info>");
        }
    }

    protected function addToGitIgnore($pattern)
    {
        if (file_exists(BASE_DIR . '/.gitignore')) {
            // check if pattern already in there, else add
            $lines = file(BASE_DIR . '/.gitignore');
            $alreadyIgnored = false;
            foreach ($lines as $line) {
                if (trim($line) === $pattern) {
                    $alreadyIgnored = true;
                    break;
                }
            }

            if (!$alreadyIgnored) {
                $fh = fopen(BASE_DIR . '/.gitignore', 'a');
                fwrite(
                    $fh,
                    $pattern . "\n"
                );
                fclose($fh);
            }
        } else {
            $fh = fopen(BASE_DIR . '/.gitignore', 'w');
            fwrite(
                $fh,
                $pattern . "\n"
            );
            fclose($fh);

        }
    }

    protected function writePhpUnitXml(InputInterface $input, OutputInterface $output)
    {
        if ($this->settings['enablePhpUnit'] && !$this->settings['customPhpUnitXml']) {
            $fh = fopen(BASE_DIR . '/phpunit.xml', 'w');
            fwrite(
                $fh,
                $this->twig->render(
                    'phpunit.xml.dist',
                    $this->settings
                )
            );
            fclose($fh);
            $output->writeln("\n<info>Config file for PHPUnit written</info>");
        }
    }

    protected function writeJsHintConfig(InputInterface $input, OutputInterface $output)
    {
        if ($this->settings['enableJsHint']) {
            $fh = fopen(BASE_DIR . '/.jshintrc', 'w');
            fwrite(
                $fh,
                $this->twig->render(
                    '.jshintrc.dist',
                    $this->settings
                )
            );
            fclose($fh);
            $output->writeln("\n<info>Config file for JSHint written</info>");
        }
    }

    protected function writePhpCsConfig(InputInterface $input, OutputInterface $output)
    {
        if ($this->settings['enablePhpCodeSniffer']) {
            $fh = fopen(BASE_DIR . '/phpcs.xml', 'w');
            fwrite(
                $fh,
                $this->twig->render(
                    'phpcs.xml.dist',
                    $this->settings
                )
            );
            fclose($fh);
            $output->writeln("\n<info>Config file for PHPCS written</info>");
        }
    }
}
