<?php

namespace Ibuildings\QaTools\Core\Composer;

use Composer\Json\JsonManipulator;
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
     * @param string $directory
     */
    public function __construct($directory)
    {
        Assertion::string($directory, 'Composer project directory ought to be a string, got "%s" of type "%s"');

        $this->directory = $directory;
    }

    public function initialise(PackageName $packageName)
    {
        RuntimeAssertion::writeable('.');
        RuntimeAssertion::pathNotExists('composer.json');

        $options = ['--no-interaction'];
        $arguments = array_merge(['composer', 'init'], $options, ['--name=' . $packageName->getName()]);
        $process = ProcessBuilder::create($arguments)->setWorkingDirectory($this->directory)->getProcess();

        if ($process->run() !== 0) {
            throw new RuntimeException(
                sprintf('Failed to initialise Composer in directory "%s": "%s"', getcwd(), $process->getErrorOutput())
            );
        }
    }

    public function verifyDevDependenciesWouldntConflict(PackageSet $packages)
    {
        $this->assertComposerInitialised();

        $configurationBackup = $this->getConfiguration();

        $options = ['--dev', '--no-update', '--no-interaction'];
        $arguments = array_merge(['composer', 'require'], $options, $packages->getDescriptors());
        $process = ProcessBuilder::create($arguments)->setWorkingDirectory($this->directory)->getProcess();

        if ($process->run() !== 0) {
            // Restore the old JSON in case Composer wrote to the Composer file anyway.
            $this->restoreConfiguration($configurationBackup);

            throw new RuntimeException(
                sprintf('Failed to add development packages to Composer file: "%s"', $process->getErrorOutput())
            );
        }

        $options = ['--dry-run', '--no-interaction'];
        $arguments = array_merge(['composer', 'install'], $options);
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
        $this->assertComposerInitialised();

        $composerFileBackup = $this->getConfiguration();

        $options = ['--dev', '--no-interaction'];
        $arguments = array_merge(['composer', 'require'], $options, $packages->getDescriptors());
        $process = ProcessBuilder::create($arguments)->setWorkingDirectory($this->directory)->getProcess();

        if ($process->run() !== 0) {
            // Restore the old JSON in case Composer wrote to the Composer file anyway.
            $this->writeComposerJson($composerFileBackup);

            throw new RuntimeException(
                sprintf('Failed to require development dependencies: "%s"', $process->getErrorOutput())
            );
        }
    }

    public function install()
    {
        $options = ['--no-interaction'];
        $arguments = array_merge(['composer', 'install'], $options);
        $process = ProcessBuilder::create($arguments)->setWorkingDirectory($this->directory)->getProcess();

        if ($process->run() !== 0) {
            throw new RuntimeException(
                sprintf('Failed to install Composer dependencies: "%s"', $process->getErrorOutput())
            );
        }
    }

    public function addConflict(Package $package)
    {
        RuntimeAssertion::writeable('composer.json');

        $packageName = $package->getName()->getName();
        $versionConstraint = $package->getVersionConstraint()->getConstraint();

        $manipulator = new JsonManipulator($this->getConfiguration()->getComposerJson());
        $manipulator->addSubNode('conflict', $packageName, $versionConstraint);

        $this->writeComposerJson($manipulator->getContents());
    }

    public function getConfiguration()
    {
        RuntimeAssertion::readable('composer.json');

        if (file_exists('composer.lock')) {
            RuntimeAssertion::readable('composer.lock');

            return Configuration::withLockedDependencies(
                file_get_contents('composer.json'),
                file_get_contents('composer.lock')
            );
        } else {
            return Configuration::withoutLockedDependencies(file_get_contents('composer.json'));
        }
    }

    public function verifyConfigurationCanBeRestored(Configuration $configuration)
    {
        RuntimeAssertion::writeable('.');
        RuntimeAssertion::file('composer.json');
        RuntimeAssertion::writeable('composer.json');

        if ($configuration->hasLockedDependencies() && is_file('composer.lock')) {
            RuntimeAssertion::file('composer.lock');
            RuntimeAssertion::writeable('composer.lock');
        }

        if (file_exists('vendor')) {
            RuntimeAssertion::directory('vendor');
            RuntimeAssertion::writeable('vendor');
        }
    }

    public function restoreConfiguration(Configuration $configuration)
    {
        $this->verifyConfigurationCanBeRestored($configuration);
        $this->writeComposerJson($configuration->getComposerJson());

        if ($configuration->hasLockedDependencies()) {
            $this->writeComposerLockJson($configuration->getComposerLockJson());
        } elseif (file_exists('composer.lock')) {
            $this->removeComposerLock();
        }

        $this->install();
    }

    private function assertComposerInitialised()
    {
        RuntimeAssertion::readable('composer.json', 'Expected a Composer project to be initialised');
    }

    /**
     * Writes the given string to the Composer file in the current working directory.
     *
     * @param string $json
     * @return string
     */
    private function writeComposerJson($json)
    {
        RuntimeAssertion::writeable('composer.json');

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
        RuntimeAssertion::writeable('composer.lock');

        return file_put_contents('composer.lock', $json);
    }

    private function removeComposerLock()
    {
        RuntimeAssertion::writeable('.');

        unlink('composer.lock');
    }
}
