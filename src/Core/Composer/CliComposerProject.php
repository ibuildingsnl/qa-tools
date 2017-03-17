<?php

namespace Ibuildings\QaTools\Core\Composer;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\ProcessBuilder;

final class CliComposerProject implements Project
{
    /**
     * @var string
     */
    private $directory;

    /**
     * @var string
     */
    private $composerBinary;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var RequireCache
     */
    private $successfulRequires;

    /**
     * @param string          $directory
     * @param string          $composerBinary
     * @param LoggerInterface $logger
     */
    public function __construct($directory, $composerBinary, LoggerInterface $logger)
    {
        Assertion::string($directory, 'Composer project directory ought to be a string, got "%s" of type "%s"');
        Assertion::string($composerBinary, 'Path to Composer binary ought to be a string, got "%s" of type "%s"');

        $this->directory = $directory;
        $this->composerBinary = $composerBinary;
        $this->logger = $logger;

        $this->successfulRequires = new RequireCache();
    }

    /**
     * @return bool
     */
    public function isInitialised()
    {
        return file_exists($this->directory . '/composer.json');
    }

    /**
     * @return void
     */
    public function initialise()
    {
        passthru('composer init', $exitCode);

        if ($exitCode !== 0) {
            throw new RuntimeException(
                sprintf(
                    "Composer project could not be initialised; 'composer init' exited with status code %d",
                    $exitCode
                ),
                ''
            );
        }
    }

    public function verifyDevDependenciesWillNotConflict(PackageSet $packages)
    {
        $targetConfiguration = $this->readConfiguration();

        try {
            $this->requireDevDependencies($packages);

            $newConfiguration = $this->readConfiguration();
            $this->successfulRequires->storeConfiguration($targetConfiguration, $packages, $newConfiguration);
        } finally {
            $this->restoreConfiguration($targetConfiguration);
        }
    }

    public function requireDevDependencies(PackageSet $packages)
    {
        $targetConfiguration = $this->readConfiguration();

        // This optimises repeated requests for new requirements. For example, this happens
        // when first calling `verifyDevDependenciesWillNotConflict()`, and then
        // `requireDevDependencies()`, like done in InstallComposerDevDependencyTaskExecutor.
        if ($this->successfulRequires->containsConfiguration($targetConfiguration, $packages)) {
            $newConfiguration = $this->successfulRequires->getConfiguration($targetConfiguration, $packages);

            $this->restoreConfiguration($newConfiguration);

            return;
        }

        $options = ['--dev', '--no-interaction'];
        $arguments = array_merge([$this->composerBinary, 'require'], $options, $packages->getDescriptors());
        $process = ProcessBuilder::create($arguments)
            ->setWorkingDirectory($this->directory)
            ->setEnv('COMPOSER_HOME', getenv('COMPOSER_HOME'))
            ->setTimeout(null)
            ->getProcess();

        if ($process->run() !== 0) {
            $packageNames = join("\n - ", $packages->getDescriptors());

            $this->logger->error("Failed to require development dependencies");
            $this->logger->error("One of these packages could not be installed (from inside $this->directory):");
            $this->logger->error(" - $packageNames");

            throw new RuntimeException(
                'Failed to require development dependencies',
                $process->getErrorOutput()
            );
        }
    }

    public function readConfiguration()
    {
        if (file_exists('composer.lock')) {
            $backup = Configuration::withLockedDependencies(
                file_get_contents('composer.json'),
                file_get_contents('composer.lock')
            );
        } else {
            $backup = Configuration::withoutLockedDependencies(file_get_contents('composer.json'));
        }

        return $backup;
    }

    public function restoreConfiguration(Configuration $configuration)
    {
        file_put_contents('composer.json', $configuration->getComposerJson());

        if ($configuration->hasLockedDependencies()) {
            file_put_contents('composer.lock', $configuration->getComposerLockJson());
        } elseif (file_exists('composer.lock')) {
            unlink('composer.lock');
        }

        $options = ['--no-interaction'];
        $arguments = array_merge([$this->composerBinary, 'install'], $options);
        $process = ProcessBuilder::create($arguments)
            ->setWorkingDirectory($this->directory)
            ->setEnv('COMPOSER_HOME', getenv('COMPOSER_HOME'))
            ->setTimeout(null)
            ->getProcess();

        if ($process->run() !== 0) {
            throw new RuntimeException(
                'Failed to install Composer dependencies',
                $process->getErrorOutput()
            );
        }
    }
}
