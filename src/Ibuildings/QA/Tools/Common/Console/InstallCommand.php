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

use Ibuildings\QA\Tools\Common\Configurator\BuildArtifactsConfigurator;
use Ibuildings\QA\Tools\Common\Configurator\Helper\MultiplePathHelper;
use Ibuildings\QA\Tools\Common\Configurator\Registry;
use Ibuildings\QA\Tools\Common\Configurator\TravisConfigurator;

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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class InstallCommand
 *
 * @package Ibuildings\QA\Tools\Common\Console
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InstallCommand extends AbstractCommand
{
    /** @var  array */
    protected $composerConfig;

    /**
     * @var \Symfony\Component\Console\Helper\DialogHelper
     */
    protected $dialog;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $filesystem;

    protected function configure()
    {
        $this
            ->setName('install')
            ->setDescription('Setup for Ibuildings QA Tools')
            ->setHelp('Installs all tools and config files');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->dialog = $this->getApplication()->getDialogHelper();
        $this->filesystem = new Filesystem();

        if ($input->isInteractive()) {
            return;
        }

        // if the qa-tools.json of a full previous run are loaded, we support the --no-interaction (-n) flag.
        // This is achieved by making the dialog input-aware, see \Symfony\Console\Helper\DialogHelper::ask()
        // lines 101-103 and \Symfony\Console\Helper\InputAwareHelper
        if (!$this->settings->hasLoadedJsonFile() || !$this->settings->previousRunWasCompleted()) {
            $output->writeln(
                '<error>Previous run was not completed fully, cannot run in non-interactive mode</error>'
            );

            // stop with error
            exit(1);
        }

        $this->dialog->setInput($input);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Starting setup of Ibuildings QA Tools<info>");

        // Test if correct ant version is installed
        $commandExistenceChecker = $this->getCommandExistenceChecker();
        if (!$commandExistenceChecker->commandExists('ant -version', $message, InstallCommand::MINIMAL_VERSION_ANT)) {
            $output->writeln("\n<error>{$message} -> Exiting.</error>");

            return;
        }

        if (!$this->dialog->askConfirmation(
            $output,
            "\nIf you already have a build config, it will be overwritten. Do you want to continue?",
            true
        )
        ) {
            return;
        }

        $output->writeln("\n");
        $this->configureProjectName($input, $output);

        // Register configurators
        $configuratorRegistry = $this->getConfiguratorRegistry();

        $configuratorRegistry->register(new BuildArtifactsConfigurator($output, $this->dialog, $this->settings));
        $configuratorRegistry->register(new TravisConfigurator($output, $this->dialog, $this->settings, $this->twig));
        // PHP
        $phpconfigurator = new PhpConfigurator($output, $this->settings);
        $this->requireHelper('dialog', $phpconfigurator);
        $configuratorRegistry->register($phpconfigurator);

        $configuratorRegistry->register(new PhpComposerConfigurator($output, $this->dialog, $this->settings));
        $configuratorRegistry->register(new PhpLintConfigurator($output, $this->dialog, $this->settings));

        $multiplePathHelper = new MultiplePathHelper($output, $this->dialog, $this->settings->getBaseDir());

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

        $configuratorRegistry->register(
            new PhpSourcePathConfigurator($output, $this->dialog, $multiplePathHelper, $this->settings)
        );
        $configuratorRegistry->register(
            new PhpUnitConfigurator($output, $this->dialog, $multiplePathHelper, $this->settings, $this->twig)
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

        $this->settings['_qa_tools_run_completed'] = true;
    }

    /**
     * Guess name based on dir
     *
     * @param string $dirName
     *
     * @return string
     */
    protected function guessName($dirName)
    {
        $guessedName = filter_var(
            ucwords(str_replace(array('-', '_'), ' ', $dirName)),
            FILTER_SANITIZE_STRING,
            FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_LOW
        );

        return $guessedName;
    }

    protected function configureProjectName(InputInterface $input, OutputInterface $output)
    {
        $dirName = basename($this->settings->getBaseDir());
        $guessedName = $this->guessName($dirName);

        $default = (empty($this->settings['projectName'])) ? $guessedName : $this->settings['projectName'];
        $this->settings['projectName'] = $this->dialog->askAndValidate(
            $output,
            "What is the name of the project? [$default] ",
            function ($data) {
                if (preg_match('/^[\w\.\s]+$/', $data)) {
                    return $data;
                }
                throw new \Exception("The project name may only contain 'a-zA-Z0-9_. '");
            },
            false,
            $default
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
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

            $written = $this->writeContentTo(
                $this->settings->getBaseDir() . '/build.xml',
                $this->twig->render('build.xml.dist', $this->settings->getArrayCopy()),
                $output
            );

            if ($written) {
                $output->writeln("\n<info>Ant build file written</info>");
            }

            $written = $this->writeContentTo(
                $this->settings->getBaseDir() . '/build-pre-commit.xml',
                $this->twig->render('build-pre-commit.xml.dist', $this->settings->getArrayCopy()),
                $output
            );

            if ($written) {
                $output->writeln("\n<info>Ant pre commit build file written</info>");
            }

            $this->addToGitIgnore('build', $output);
            $this->addToGitIgnore('cache.properties', $output);

            $output->writeln("\n<info>Ant build file written</info>");
        } else {
            $output->writeln("\n<info>No QA tools enabled. No configuration written</info>");
        }
    }

    protected function writeContentTo($toFile, $content, OutputInterface $output)
    {
        try {
            $this->filesystem->dumpFile($toFile, $content);
        } catch (IOException $e) {
            $output->writeln(sprintf(
                '<error>Could not write content to file "%s"</error>',
                $toFile
            ));
            return false;
        }

        return true;
    }

    protected function addToGitIgnore($pattern, OutputInterface $output)
    {
        $file = $this->settings->getBaseDir() . '/.gitignore';
        $patternLine = $pattern . "\n";
        $lines = array();

        if ($this->filesystem->exists($file)) {
            $lines = file($file);
            if ($lines === false) {
                $output->writeln(sprintf(
                    '<error>Could not add pattern "%s" to .gitignore file, please do so manually</error>',
                    $pattern
                ));
                return;
            }

            if (in_array($patternLine, $lines)) {
                return;
            }
        }

        $lines[] = $patternLine;
        if (!$this->writeContentTo($file, $patternLine, $output)) {
            $output->writeln(sprintf(
                '<error>Could not add pattern "%s" to .gitignore file, please do so manually</error>',
                $pattern
            ));
        }
    }

    protected function getConfiguratorRegistry()
    {
        return new Registry();
    }
}
