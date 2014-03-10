<?php

/**
 * This file is part of Ibuildings QA-Tools.
 *
 * (c) Ibuildings
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ibuildings\QA\Tools\Common\Console;

use Ibuildings\QA\Tools\Common\Configurator\Helper\MultiplePathHelper;
use Ibuildings\QA\Tools\PHP\Configurator\PhpComposerConfigurator;

use Ibuildings\QA\Tools\PHP\Configurator\PhpConfigurator;
use Ibuildings\QA\Tools\PHP\Configurator\PhpCodeSnifferConfigurator;
use Ibuildings\QA\Tools\PHP\Configurator\PhpCopyPasteDetectorConfigurator;
use Ibuildings\QA\Tools\PHP\Configurator\PhpLintConfigurator;
use Ibuildings\QA\Tools\PHP\Configurator\PhpMessDetectorConfigurator;
use Ibuildings\QA\Tools\PHP\Configurator\PhpSecurityCheckerConfigurator;
use Ibuildings\QA\Tools\PHP\Configurator\PhpSourcePathConfigurator;
use Ibuildings\QA\Tools\PHP\Configurator\PhpUnitConfigurator;

use Ibuildings\QA\Tools\Javascript\Configurator\JavascriptConfigurator;
use Ibuildings\QA\Tools\Javascript\Configurator\JsHintConfigurator;
use Ibuildings\QA\Tools\Javascript\Configurator\JavascriptSourcePathConfigurator;

use Ibuildings\QA\Tools\Functional\Configurator\BehatConfigurator;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InstallCommand
 *
 * @package Ibuildings\QA\Tools\Common\Console
 *
 * @SuppressWarnings(PHPMD)
 */
class InstallCommand extends AbstractCommand
{
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
        parent::initialize($input, $output);

        $this->enableDefaultSettings();
    }

    private function enableDefaultSettings()
    {
        $this->settings['buildArtifactsPath'] = 'build/artifacts';

        return $this;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Starting setup of Ibuildings QA Tools<info>");

        // Test if correct ant version is installed
        $commandExistenceChecker = $this->getCommitExistenceChecker();
        if (!$commandExistenceChecker->commandExists('ant -version', $message, InstallCommand::MINIMAL_VERSION_ANT)) {
            $output->writeln("\n<error>{$message} -> Exiting.</error>");

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

        // Register configurators
        $configuratorRegistry = $this->getConfiguratorRegistry();

        $multiplePathHelper = new MultiplePathHelper($output, $this->dialog, $this->settings->getBaseDir());

        // PHP
        $configuratorRegistry->register(new PhpConfigurator($output, $this->dialog, $this->settings));
        $configuratorRegistry->register(new PhpComposerConfigurator($output, $this->dialog, $this->settings));
        $configuratorRegistry->register(new PhpLintConfigurator($output, $this->dialog, $this->settings));
        $configuratorRegistry->register(
            new PhpMessDetectorConfigurator($output, $this->dialog, $multiplePathHelper, $this->settings, $this->twig)
        );
        $configuratorRegistry->register(
            new PhpCodeSnifferConfigurator($output, $this->dialog, $multiplePathHelper, $this->settings, $this->twig)
        );
        $configuratorRegistry->register(
            new PhpCopyPasteDetectorConfigurator($output, $this->dialog, $multiplePathHelper, $this->settings)
        );
        $configuratorRegistry->register(
            new PhpSecurityCheckerConfigurator($output, $this->dialog, $this->settings)
        );

        $configuratorRegistry->register(new PhpSourcePathConfigurator($output, $this->dialog, $this->settings));
        $configuratorRegistry->register(
            new PhpUnitConfigurator($output, $this->dialog, $this->settings, $this->twig)
        );

        // Javascript
        $configuratorRegistry->register(new JavascriptConfigurator($output, $this->dialog, $this->settings));
        $installJsHintCommand = $this->getApplication()->find('install:jshint');
        $configuratorRegistry->register(new JsHintConfigurator(
            $input,
            $output,
            $this->dialog,
            $this->settings,
            $this->twig,
            $installJsHintCommand
        ));
        $configuratorRegistry->register(new JavascriptSourcePathConfigurator($output, $this->dialog, $this->settings));

        // Functional testing
        $configuratorRegistry->register(
            new BehatConfigurator($output, $this->dialog, $this->settings, $this->twig)
        );

        $configuratorRegistry->executeConfigurators();

        $this->writeAntBuildXml($input, $output);

        $command = $this->getApplication()->find('install:pre-commit');
        $command->run($input, $output);
    }

    protected function configureProjectName(InputInterface $input, OutputInterface $output)
    {
        $dirName = basename($this->settings->getBaseDir());
        $guessedName = filter_var(
            ucwords(str_replace(array('-', '_'), ' ', $dirName)),
            FILTER_SANITIZE_STRING,
            FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_LOW
        );

        $this->settings['projectName'] = $this->dialog->askAndValidate(
            $output,
            "What is the name of the project? [$guessedName] ",
            function ($data) {
                if (preg_match('/^[\w\.\s]+$/', $data)) {
                    return $data;
                }
                throw new \Exception("The project name may only contain 'a-zA-Z0-9_. '");
            },
            false,
            $guessedName
        );
    }

    protected function configureBuildArtifactsPath(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->dialog;
        $settings = $this->settings;
        $this->settings['buildArtifactsPath'] = $this->dialog->askAndValidate(
            $output,
            "Where do you want to store the build artifacts? [" . $this->settings['buildArtifactsPath'] . "] ",
            function ($data) use ($output, $dialog, $settings) {
                if (!is_dir($settings->getBaseDir() . '/' . $data)) {
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

    protected function writeAntBuildXml(InputInterface $input, OutputInterface $output)
    {
        if ($this->settings['enablePhpMessDetector']
            || $this->settings['enablePhpCopyPasteDetection']
            || $this->settings['enablePhpCodeSniffer']
            || $this->settings['enablePhpUnit']
            || $this->settings['enablePhpLint']
            || $this->settings['enableJsHint']
            || $this->settings['enableBehat']
        ) {

            $this->writeRenderedContentTo(
                $this->settings->getBaseDir() . '/build.xml',
                'build.xml.dist',
                $this->settings->getArrayCopy()
            );

            $output->writeln("\n<info>Ant build file written</info>");

            $this->writeRenderedContentTo(
                $this->settings->getBaseDir() . '/build-pre-commit.xml',
                'build-pre-commit.xml.dist',
                $this->settings->getArrayCopy()
            );

            $output->writeln("\n<info>Ant pre commit build file written</info>");

            $this->addToGitIgnore('build');
            $this->addToGitIgnore('cache.properties');

            $output->writeln("\n<info>Ant build file written</info>");
        } else {
            $output->writeln("\n<info>No QA tools enabled. No configuration written</info>");
        }
    }

    protected function writeRenderedContentTo($toFile, $templateName, $params)
    {
        $fh = fopen($toFile, 'w');
        fwrite(
            $fh,
            $this->twig->render(
                $templateName,
                $params
            )
        );
        fclose($fh);
    }

    protected function addToGitIgnore($pattern)
    {
        if (file_exists($this->settings->getBaseDir() . '/.gitignore')) {
            // check if pattern already in there, else add
            $lines = file($this->settings->getBaseDir() . '/.gitignore');
            $alreadyIgnored = false;
            foreach ($lines as $line) {
                if (trim($line) === $pattern) {
                    $alreadyIgnored = true;
                    break;
                }
            }

            if (!$alreadyIgnored) {
                $fh = fopen($this->settings->getBaseDir() . '/.gitignore', 'a');
                fwrite(
                    $fh,
                    $pattern . "\n"
                );
                fclose($fh);
            }
        } else {
            $fh = fopen($this->settings->getBaseDir() . '/.gitignore', 'w');
            fwrite(
                $fh,
                $pattern . "\n"
            );
            fclose($fh);
        }
    }
}
