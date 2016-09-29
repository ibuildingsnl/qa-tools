<?php

namespace Ibuildings\QaTools\Core\Composer;

use Ibuildings\QaTools\Core\Assert\Assertion;
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
        $this->backUpConfiguration();

        $options = ['--dev', '--no-update', '--no-interaction'];
        $arguments = array_merge([$this->composerBinary, 'require'], $options, $packages->getDescriptors());
        $process = ProcessBuilder::create($arguments)->setWorkingDirectory($this->directory)->getProcess();

        if ($process->run() !== 0) {
            // Restore the old JSON in case Composer wrote to the Composer file anyway.
            $this->restoreConfiguration();

            throw new RuntimeException(
                'Failed to add development packages to Composer file',
                $process->getErrorOutput()
            );
        }

        $options = ['--dry-run', '--no-interaction'];
        $arguments = array_merge([$this->composerBinary, 'install'], $options);
        $process = ProcessBuilder::create($arguments)->setWorkingDirectory($this->directory)->getProcess();

        if ($process->run() !== 0) {
            $this->restoreConfiguration();

            throw new RuntimeException(
                'Failed to dry-run Composer packages installation',
                $process->getErrorOutput()
            );
        }

        // Restore the old JSON to remove the added development dependencies.
        $this->restoreConfiguration();
    }

    public function requireDevDependencies(PackageSet $packages)
    {
        $options = ['--dev', '--no-interaction'];
        $arguments = array_merge([$this->composerBinary, 'require'], $options, $packages->getDescriptors());
        $process = ProcessBuilder::create($arguments)->setWorkingDirectory($this->directory)->getProcess();

        if ($process->run() !== 0) {
            throw new RuntimeException(
                'Failed to require development dependencies',
                $process->getErrorOutput()
            );
        }
    }

    public function backUpConfiguration()
    {
        if (file_exists('composer.lock')) {
            $this->configurationBackup = Configuration::withLockedDependencies(
                file_get_contents('composer.json'),
                file_get_contents('composer.lock')
            );
        } else {
            $this->configurationBackup = Configuration::withoutLockedDependencies(file_get_contents('composer.json'));
        }
    }

    public function restoreConfiguration()
    {
        $this->writeComposerJson($this->configurationBackup->getComposerJson());

        if ($this->configurationBackup->hasLockedDependencies()) {
            $this->writeComposerLockJson($this->configurationBackup->getComposerLockJson());
        } elseif (file_exists('composer.lock')) {
            $this->removeComposerLock();
        }

        $options = ['--no-interaction'];
        $arguments = array_merge([$this->composerBinary, 'install'], $options);
        $process = ProcessBuilder::create($arguments)->setWorkingDirectory($this->directory)->getProcess();

        if ($process->run() !== 0) {
            throw new RuntimeException(
                'Failed to install Composer dependencies',
                $process->getErrorOutput()
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
