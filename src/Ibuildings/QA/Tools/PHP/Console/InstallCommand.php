<?php
/**
 * @author Matthijs van den Bos <matthijs@vandenbos.org>
 * @copyright 2013 Matthijs van den Bos
 */

namespace Ibuildings\QA\Tools\PHP\Console;

use Ibuildings\QA\Tools\Common\DependencyInjection\Twig;
use Ibuildings\QA\Tools\Common\PHP\Configurator\PhpCodeSnifferConfigurator;
use Ibuildings\QA\Tools\Common\PHP\Configurator\PhpCopyPasteDetectorConfigurator;
use Ibuildings\QA\Tools\Common\PHP\Configurator\PhpMessDetectorConfigurator;
use Ibuildings\QA\Tools\Common\PHP\Configurator\PhpSecurityCheckerConfigurator;
use Ibuildings\QA\Tools\Common\PHP\Configurator\PhpSourcePathConfigurator;
use Ibuildings\QA\Tools\Common\PHP\Configurator\PhpUnitConfigurator;
use Ibuildings\QA\Tools\Common\Settings;

use Ibuildings\QA\Tools\Common\Configurator\Registry;
use Ibuildings\QA\Tools\Common\PHP\Configurator\PhpLintConfigurator;

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
    /** @var  Settings */
    protected $settings;

    /** @var DialogHelper */
    protected $dialog;

    /** @var \Twig_Environment */
    protected $twig;

    /** @var  array */
    protected $composerConfig;

    protected function configure()
    {
        $this
            ->setName('install')
            ->setDescription('Setup for Ibuildings QA Tools')
            ->setHelp('Installs all tools and config files');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->settings = new Settings();

        $this->dialog = $this->getHelperSet()->get('dialog');

        $twigBuilder = new Twig();
        $this->twig = $twigBuilder->create();

        $this->parseComposerConfig();

        $this->enableDefaultSettings();
    }

    /**
     * @throws \Exception
     * @return array
     */
    protected function parseComposerConfig()
    {
        if (!file_exists(BASE_DIR . DIRECTORY_SEPARATOR . 'composer.json')) {
            throw new \Exception("Could not find composer.json in project root dir '" . BASE_DIR . "'");
        }

        $file = file_get_contents(BASE_DIR . DIRECTORY_SEPARATOR . 'composer.json');

        $parsedFile = json_decode($file, true);

        if ($parsedFile === null) {
            throw new \Exception("Could not read composer.json. Is it valid JSON?");
        }

        $this->composerConfig = array();

        if (array_key_exists('config', $parsedFile)) {
            $this->composerConfig = $parsedFile['config'];
        }
    }

    private function enableDefaultSettings()
    {
        $this->settings['buildArtifactsPath'] = 'build/artifacts';

        $this->settings['enableJsHint'] = false;

        if (!is_array($this->composerConfig)) {
            throw new \Exception('Could not determine Composer config. Aborting...');
        }

        if (array_key_exists('bin-dir', $this->composerConfig)) {
            $this->settings['composerBinDir'] = $this->composerConfig['bin-dir'];
        } else {
            $this->settings['composerBinDir'] = 'vendor/bin';
        }

        return $this;
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
        )
        ) {
            return;
        }

        $output->writeln("\n");
        $this->configureProjectName($input, $output);
        $this->configureBuildArtifactsPath($input, $output);

        if ($this->dialog->askConfirmation(
            $output,
            "\n<comment>Do you want to install the QA tools for PHP? [Y/n] </comment>",
            true
        )
        ) {
            $output->writeln("\n<info>Configuring PHP inspections</info>\n");

            // Register configurators
            $configuratorRegistry = new Registry();
            $configuratorRegistry->register(new PhpLintConfigurator($output, $this->dialog, $this->settings));
            $configuratorRegistry->register(new PhpMessDetectorConfigurator($output, $this->dialog, $this->settings));
            $configuratorRegistry->register(new PhpCodeSnifferConfigurator($output, $this->dialog, $this->settings));
            $configuratorRegistry->register(new PhpCopyPasteDetectorConfigurator($output, $this->dialog, $this->settings));
            $configuratorRegistry->register(new PhpSecurityCheckerConfigurator($output, $this->dialog, $this->settings));
            $configuratorRegistry->register(new PhpSourcePathConfigurator($output, $this->dialog, $this->settings));
            $configuratorRegistry->register(new PhpUnitConfigurator($output, $this->dialog, $this->settings));
            $configuratorRegistry->executeConfigurators();

            $this->writePhpUnitXml($input, $output);
            $this->writePhpCsConfig($input, $output);
            $this->writePhpMdConfig($input, $output);
        }

        if ($this->dialog->askConfirmation(
            $output,
            "\n<comment>Do you want to install the QA tools for Javascript? [Y/n] </comment>",
            true
        )
        ) {
            $this->configureJsHint($input, $output);
            $this->configureJavaScriptSrcPath($input, $output);

            $this->writeJsHintConfig($input, $output);
        }
        $this->writeAntBuildXml($input, $output);

        //        $command = $this->getApplication()->find('install:pre-push');
        //        $command->run($input, $output);

        $command = $this->getApplication()->find('install:pre-commit');
        $command->run($input, $output);
    }

    private function commandExists($cmd)
    {
        $returnVal = shell_exec("command -v $cmd");
        return (empty($returnVal) ? false : true);
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

    protected function configureBuildArtifactsPath(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->dialog;
        $this->settings['buildArtifactsPath'] = $this->dialog->askAndValidate(
            $output,
            "Where do you want to store the build artifacts? [" . $this->settings['buildArtifactsPath'] . "] ",
            function ($data) use ($output, $dialog) {
                if (!is_dir(BASE_DIR . '/' . $data)) {
                    if ($dialog->askConfirmation(
                        $output,
                        "  - Are you sure? The path doesn't exist and will be created. [Y/n] ",
                        true
                    )
                    ) {
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

    protected function writePhpUnitXml(InputInterface $input, OutputInterface $output)
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
            $output->writeln("\n<info>Config file for PHPUnit written</info>");
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
                    $this->settings->toArray()
                )
            );
            fclose($fh);
            $output->writeln("\n<info>Config file for PHP Code Sniffer written</info>");
        }
    }

    protected function writePhpMdConfig(InputInterface $input, OutputInterface $output)
    {
        if ($this->settings['enablePhpMessDetector']) {
            $fh = fopen(BASE_DIR . '/phpmd.xml', 'w');
            fwrite(
                $fh,
                $this->twig->render(
                    'phpmd.xml.dist',
                    $this->settings->toArray()
                )
            );
            fclose($fh);
            $output->writeln("\n<info>Config file for PHP Mess Detector written</info>");
        }
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

    protected function writeJsHintConfig(InputInterface $input, OutputInterface $output)
    {
        if ($this->settings['enableJsHint']) {
            $fh = fopen(BASE_DIR . '/.jshintrc', 'w');
            fwrite(
                $fh,
                $this->twig->render(
                    '.jshintrc.dist',
                    $this->settings->toArray()
                )
            );
            fclose($fh);
            $output->writeln("\n<info>Config file for JSHint written</info>");
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
                    $this->settings->toArray()
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
}
