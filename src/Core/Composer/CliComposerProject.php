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
     * @var Configuration|null
     */
    private $configurationBackup;

    /**
     * @var LoggerInterface
     */
    private $logger;

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
        $configuration = $this->readConfiguration();

        try {
            $this->requireDevDependencies($packages);
        } finally {
            $this->installConfiguration($configuration);
        }
    }

    public function requireDevDependencies(PackageSet $packages)
    {
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

    public function backUpConfiguration()
    {
        $this->configurationBackup = $this->readConfiguration();
    }

    public function restoreConfiguration()
    {
        $this->installConfiguration($this->configurationBackup);
    }

    /**
     * Writes the given string to the Composer file in the current working directory.
     *
     * @param string $json
     * @return string
     */
    private function writeComposerJson($json)
    {
        return file_put_contents('composer.json', $json);
    }

    /**
     * Writes the given string to the Composer lock file in the current working directory.
     *
     * @param string $json
     * @return string
     */
    private function writeComposerLockJson($json)
    {
        return file_put_contents('composer.lock', $json);
    }

    private function removeComposerLock()
    {
        unlink('composer.lock');
    }

    /**
     * Returns the Composer configuration currently on disk.
     *
     * @return Configuration
     */
    private function readConfiguration()
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

    /**
     * Writes the given Composer configuration to disk and installs the locked dependencies.
     *
     * @param Configuration $configuration
     * @return void
     */
    private function installConfiguration(Configuration $configuration)
    {
        $this->writeComposerJson($configuration->getComposerJson());

        if ($configuration->hasLockedDependencies()) {
            $this->writeComposerLockJson($configuration->getComposerLockJson());
        } elseif (file_exists('composer.lock')) {
            $this->removeComposerLock();
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
