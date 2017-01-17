<?php

namespace Ibuildings\QaTools\Core\Npm;

use Ibuildings\QaTools\Core\Cli\Cli;
use Ibuildings\QaTools\Core\Composer\RuntimeException;
use Ibuildings\QaTools\Core\Project\Directory;
use Symfony\Component\Process\ProcessBuilder;

class CliNpmProject implements NpmProject
{
    /** @var Directory */
    private $directory;
    /** @var string */
    private $npmExecutable;

    /**
     * CliNpmProject constructor.
     * @param Directory       $directory
     * @param string          $npmExecutable
     */
    public function __construct(Directory $directory, $npmExecutable)
    {
        $this->directory = $directory;
        $this->npmExecutable = $npmExecutable;
    }

    /**
     * @return bool
     */
    public function isInitialised()
    {
        return file_exists($this->directory->getDirectory().'/package.json');
    }

    /**
     * Initialise package.json
     * @throws \Symfony\Component\Process\Exception\LogicException
     * @throws \Ibuildings\QaTools\Core\Composer\RuntimeException
     * @throws \Symfony\Component\Process\Exception\RuntimeException
     */
    public function initialise()
    {
        if (!Cli::isExecutableInstalled($this->npmExecutable)) {
            throw new RuntimeException('Unable to initialise NPM because NPM is not installed', '');
        }

        $process = ProcessBuilder::create([$this->npmExecutable, 'init', '--yes'])->getProcess();
        if ($process->run() !== 0) {
            throw new RuntimeException('Unable to initialise NPM. Is NPM installed?', $process->getOutput());
        }
    }

    /**
     * Verify if a given set of NPM packages can be installed
     *
     * @param array $packages
     * @return bool
     * @throws \Symfony\Component\Process\Exception\RuntimeException
     * @throws \Symfony\Component\Process\Exception\LogicException
     */
    public function verifyDevDependenciesCanBeInstalled(array $packages)
    {
        if (!Cli::isExecutableInstalled($this->npmExecutable)) {
            return false;
        }

        $process = ProcessBuilder::create(array_merge([$this->npmExecutable, 'install', '--dry-run'], $packages))
            ->setWorkingDirectory($this->directory->getDirectory())
            ->getProcess();

        return $process->run() === 0;
    }

    /**
     * Install a given set of NPM packages
     *
     * @param array $packages
     * @throws \Symfony\Component\Process\Exception\LogicException
     * @throws \Symfony\Component\Process\Exception\RuntimeException
     */
    public function installDevDependencies(array $packages)
    {
        $process = ProcessBuilder::create(array_merge([$this->npmExecutable, 'install'], $packages))
            ->setWorkingDirectory($this->directory->getDirectory())
            ->getProcess();

        $process->run();
    }
}
