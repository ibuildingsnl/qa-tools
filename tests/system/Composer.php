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
        $configuration = [
            // Emulate all the tools' Composer packages locally to guarantee test
            // reliability by removing the Internet factor and to speed up tests.
            'repositories' => [
                ['packagist' => false],
                ['type' => 'path', 'url' => __DIR__ . '/../composer/packages/phpmd'],
                ['type' => 'path', 'url' => __DIR__ . '/../composer/packages/phpcs'],
                ['type' => 'artifact', 'url' => __DIR__ . '/../composer/packages/drupal'],
                ['type' => 'path', 'url' => __DIR__ . '/../composer/packages/escapestudios'],
            ],
        ];
        file_put_contents('composer.json', json_encode($configuration, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
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
}
