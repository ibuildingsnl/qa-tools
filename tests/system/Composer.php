<?php

namespace Ibuildings\QaTools\SystemTest;

use Composer\Json\JsonManipulator;
use Ibuildings\QaTools\Core\Exception\RuntimeException;
use Symfony\Component\Process\ProcessBuilder;

final class Composer
{
    /**
     * Initialise a new Composer project.
     */
    public static function initialise()
    {
        file_put_contents('composer.json', '{}');
    }

    /**
     * Adds a dependency conflict.
     *
     * @param string $packageName
     * @param string $packageVersionConstraint
     * @return void
     */
    public static function addConflict($packageName, $packageVersionConstraint)
    {
        $manipulator = new JsonManipulator(file_get_contents('composer.json'));
        $manipulator->addSubNode('conflict', $packageName, $packageVersionConstraint);

        file_put_contents('composer.json', $manipulator->getContents());
    }

    /**
     * Performs a `composer install` in the current working directory.
     *
     * @return void
     */
    public static function install()
    {
        $process = ProcessBuilder::create(['composer', 'install'])->getProcess();

        if ($process->run() !== 0) {
            throw new RuntimeException(sprintf('Composer install failed: %s', $process->getErrorOutput()));
        }
    }

    /**
     * @param string $packageName
     */
    public static function assertPackageIsInstalled($packageName)
    {
        assertFileExists(sprintf('vendor/%s/composer.json', $packageName));
    }

    /**
     * @param string $packageName
     */
    public static function assertPackageIsNotInstalled($packageName)
    {
        assertFileNotExists(sprintf('vendor/%s', $packageName));
    }

    /**
     * @param string $packageName
     */
    public static function assertPackageIsNotRequiredAsDevDependency($packageName)
    {
        assertFileExists('composer.json');

        $composerConfiguration = json_decode(file_get_contents('composer.json'), true);

        if (!array_key_exists('require-dev', $composerConfiguration)) {
            return;
        }
        if (!array_key_exists($packageName, $composerConfiguration['require-dev'])) {
            return;
        }

        fail(sprintf('Package "%s" is required as a development dependency', $packageName));
    }
}
