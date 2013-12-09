<?php
/**
 * @author Matthijs van den Bos <matthijs@vandenbos.org>
 * @copyright 2013 Matthijs van den Bos
 */

namespace Ibuildings\QA\Tools\Common\Console;

use Ibuildings\QA\Tools\Common\Configurator\Helper\MultiplePathHelper;
use Ibuildings\QA\Tools\PHP\Configurator\PhpExcludePathsConfigurator;
use Ibuildings\QA\Tools\Common\Settings;
use Ibuildings\QA\Tools\Common\Configurator\Registry;
use Ibuildings\QA\Tools\Common\DependencyInjection\Twig;
use Ibuildings\QA\Tools\Common\CommandExistenceChecker;
use Ibuildings\QA\Tools\PHP\Configurator\PhpCodeSnifferConfigurator;
use Ibuildings\QA\Tools\PHP\Configurator\PhpCopyPasteDetectorConfigurator;
use Ibuildings\QA\Tools\PHP\Configurator\PhpLintConfigurator;
use Ibuildings\QA\Tools\PHP\Configurator\PhpMessDetectorConfigurator;
use Ibuildings\QA\Tools\PHP\Configurator\PhpSecurityCheckerConfigurator;
use Ibuildings\QA\Tools\PHP\Configurator\PhpSourcePathConfigurator;
use Ibuildings\QA\Tools\PHP\Configurator\PhpUnitConfigurator;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class InstallCommand
 *
 * @package Ibuildings\QA\Tools\PHP\Console
 *
 * @SuppressWarnings(PHPMD)
 */
class InstallCommand extends Command
{
    /**
     * Lowest version of ant on which QA Tools is known to work
     * This could be increased to 1.8 since that makes it possible to use variables
     * instead of property names for if/unless constructs
     * see: https://ant.apache.org/manual/properties.html#if+unless
     */
    const MINIMAL_VERSION_ANT = '1.7.1';
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

        // Test if correct ant version is installed
        $commandExistenceChecker = new CommandExistenceChecker();
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

        if ($this->dialog->askConfirmation(
            $output,
            "\n<comment>Do you want to install the QA tools for PHP? [Y/n] </comment>",
            true
        )
        ) {
            $output->writeln("\n<info>Configuring PHP inspections</info>\n");

            $multiplePathHelper = new MultiplePathHelper($output, $this->dialog, BASE_DIR);

            // Register configurators
            $configuratorRegistry = new Registry();
            $configuratorRegistry->register(new PhpLintConfigurator($output, $this->dialog, $this->settings));
            $configuratorRegistry->register(
                new PhpMessDetectorConfigurator($output, $this->dialog, $this->settings, $this->twig)
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
            $configuratorRegistry->executeConfigurators();
        }

        $this->settings['enableJsTools'] = false;
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

        if ($this->dialog->askConfirmation(
            $output,
            "\n<comment>Do you want to install the Behat framework? [Y/n] </comment>",
            true
        )
        ) {
            $this->configureBehat($input, $output);
            $this->writeBehatYamlFiles($input, $output);
            $this->writeBehatExamples($input, $output);
        }

        $this->writeAntBuildXml($input, $output);

        //        $command = $this->getApplication()->find('install:pre-push');
        //        $command->run($input, $output);

        $command = $this->getApplication()->find('install:pre-commit');
        $command->run($input, $output);
    }

    protected function configureProjectName(InputInterface $input, OutputInterface $output)
    {
        $dirName = basename(BASE_DIR);
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

    protected function configureJsHint(InputInterface $input, OutputInterface $output)
    {
        $this->settings['enableJsHint'] = $this->dialog->askConfirmation(
            $output,
            "Do you want to enable JSHint? [Y/n] ",
            true
        );

        if ($this->settings['enableJsHint']) {
            $this->settings['enableJsTools'] = true;
        }

        // Test if node is installed
        $commandExistenceChecker = new CommandExistenceChecker();
        if (!$commandExistenceChecker->commandExists('node', $message)) {
            $output->writeln("\n<error>{$message} -> Not enabling JSHint.</error>");
            $this->settings['enableJsHint'] = false;

            return;
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

    /**
     * Enable in the settings if Behat is available.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function configureBehat(InputInterface $input, OutputInterface $output)
    {
        $this->settings['enableBehat'] = true;
        $this->settings['featuresDir'] = BASE_DIR . '/features';
    }

    /**
     * Install Behat yaml files.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function writeBehatYamlFiles(InputInterface $input, OutputInterface $output)
    {
        if (!$this->settings['enableBehat']) {
            return;
        }

        $this->settings['baseUrl'] = $this->dialog->askAndValidate(
            $output,
            "What is base url of your application? [http://www.ibuildings.nl] ",
            function ($data) {
                if (substr($data, 0, 4) == 'http') {
                    return $data;
                }
                throw new \Exception("Url needs to start with http");
            },
            false,
            'http://www.ibuildings.nl'
        );

        $baseUrlCi = $this->suggestDomain($this->settings['baseUrl'], 'ci');

        $this->settings['baseUrlCi'] = $this->dialog->askAndValidate(
            $output,
            "What is base url of the ci environment? [$baseUrlCi] ",
            function ($data) {
                if (substr($data, 0, 4) == 'http') {
                    return $data;
                }
                throw new \Exception("Url needs to start with http");
            },
            false,
            $baseUrlCi
        );

        $baseUrlDev = $this->suggestDomain($this->settings['baseUrl'], 'dev');

        $this->settings['baseUrlDev'] = $this->dialog->askAndValidate(
            $output,
            "What is base url of your dev environment? [$baseUrlDev] ",
            function ($data) {
                if (substr($data, 0, 4) == 'http') {
                    return $data;
                }
                throw new \Exception("Url needs to start with http");
            },
            false,
            $baseUrlDev
        );

        // copy behat.yml
        $fh = fopen(BASE_DIR . '/behat.yml', 'w');
        fwrite(
            $fh,
            $this->twig->render(
                'behat.yml.dist',
                $this->settings->toArray()
            )
        );
        fclose($fh);

        // copy behat.yml
        $fh = fopen(BASE_DIR . '/behat.dev.yml', 'w');
        fwrite(
            $fh,
            $this->twig->render(
                'behat.dev.yml.dist',
                $this->settings->toArray()
            )
        );
        fclose($fh);
    }

    /**
     * Suggest a new domain based on the 'main url' and a subdomain string.
     *
     * @param string $url the main domain
     * @param string $part the subdomain string
     *
     * @return string
     */
    protected function suggestDomain($url, $part)
    {
        $urlParts = parse_url($url);

        $scheme = $urlParts['scheme'];
        $host = $urlParts['host'];

        if (strrpos($host, 'www') !== false) {
            return $scheme . '://' . str_replace('www', $part, $host);
        }

        $hostParts = explode('.', $host);
        if (count($hostParts) > 2) {
            // change first part of the hostname
            $hostParts[0] = $part;

            return $scheme . '://' . implode('.', $hostParts);
        } else {
            // prefix hostname
            return $scheme . '://' . $part . '.' . implode('.', $hostParts);
        }
    }

    /**
     * Install a Behat feature example.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function writeBehatExamples(InputInterface $input, OutputInterface $output)
    {
        if (!$this->settings['enableBehat']) {
            return;
        }

        if (is_dir($this->settings['featuresDir'])) {
            $output->writeln("<error>Features directory already present. No example features are installed.</error>");

            return;
        }

        try {
            $filesystem = new Filesystem();
            $filesystem->mirror(PACKAGE_BASE_DIR . '/config-dist/features', $this->settings['featuresDir']);
        } catch (Exception $e) {
            $output->writeln(
                "<error>Something went wrong when creating the features directory" . $e->getMessage() . "</error>"
            );

            return;
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

            $output->writeln("\n<info>Ant build file written</info>");

            $fh = fopen(BASE_DIR . '/build-pre-commit.xml', 'w');
            fwrite(
                $fh,
                $this->twig->render(
                    'build-pre-commit.xml.dist',
                    $this->settings->toArray()
                )
            );
            fclose($fh);

            $output->writeln("\n<info>Ant pre commit build file written</info>");

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
