<?php

namespace Ibuildings\QaTools\Core\Composer;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Exception\RuntimeException;
use Symfony\Component\Process\ProcessBuilder;

final class CliComposerProject implements Project
{
    /**
     * @var string
     */
    private $directory;

    /**
     * @var
     */
    private $composerBinary;

    /**
     * @param string $directory
     * @param string $composerBinary
     */
    public function __construct($directory, $composerBinary)
    {
        Assertion::string($directory, 'Composer project directory ought to be a string, got "%s" of type "%s"');
        Assertion::string($composerBinary, 'Path to Composer binary ought to be a string, got "%s" of type "%s"');

        $this->directory = $directory;
        $this->composerBinary = $composerBinary;
    }

    public function verifyDevDependenciesWillNotConflict(PackageSet $packages)
    {
        $configurationBackup = $this->getConfiguration();

        $options = ['--dev', '--no-update', '--no-interaction'];
        $arguments = array_merge([$this->composerBinary, 'require'], $options, $packages->getDescriptors());
        $process = ProcessBuilder::create($arguments)->setWorkingDirectory($this->directory)->getProcess();

        if ($process->run() !== 0) {
            // Restore the old JSON in case Composer wrote to the Composer file anyway.
            $this->restoreConfiguration($configurationBackup);

            throw new RuntimeException(
                sprintf('Failed to add development packages to Composer file: "%s"', $process->getErrorOutput())
            );
        }

        $options = ['--dry-run', '--no-interaction'];
        $arguments = array_merge([$this->composerBinary, 'install'], $options);
        $process = ProcessBuilder::create($arguments)->setWorkingDirectory($this->directory)->getProcess();

        if ($process->run() !== 0) {
            $this->restoreConfiguration($configurationBackup);

            throw new RuntimeException(
                sprintf('Failed to dry-run Composer packages installation: "%s"', $process->getErrorOutput())
            );
        }

        // Restore the old JSON to remove the added development dependencies.
        $this->restoreConfiguration($configurationBackup);
    }

    public function requireDevDependencies(PackageSet $packages)
    {
        $composerFileBackup = $this->getConfiguration();

        $options = ['--dev', '--no-interaction'];
        $arguments = array_merge([$this->composerBinary, 'require'], $options, $packages->getDescriptors());
        $process = ProcessBuilder::create($arguments)->setWorkingDirectory($this->directory)->getProcess();

        if ($process->run() !== 0) {
            // Restore the old JSON in case Composer wrote to the Composer file anyway.
            $this->writeComposerJson($composerFileBackup);

            throw new RuntimeException(
                sprintf('Failed to require development dependencies: "%s"', $process->getErrorOutput())
            );
        }
    }

    public function getConfiguration()
    {
        if (file_exists('composer.lock')) {
            return Configuration::withLockedDependencies(
                file_get_contents('composer.json'),
                file_get_contents('composer.lock')
            );
        } else {
            return Configuration::withoutLockedDependencies(file_get_contents('composer.json'));
        }
    }

    public function restoreConfiguration(Configuration $configuration)
    {
        $this->writeComposerJson($configuration->getComposerJson());

        if ($configuration->hasLockedDependencies()) {
            $this->writeComposerLockJson($configuration->getComposerLockJson());
        } elseif (file_exists('composer.lock')) {
            $this->removeComposerLock();
        }

        $options = ['--no-interaction'];
        $arguments = array_merge([$this->composerBinary, 'install'], $options);
        $process = ProcessBuilder::create($arguments)->setWorkingDirectory($this->directory)->getProcess();

        if ($process->run() !== 0) {
            throw new RuntimeException(
                sprintf('Failed to install Composer dependencies: "%s"', $process->getErrorOutput())
            );
        }
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
}
