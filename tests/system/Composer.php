<?php

namespace Ibuildings\QaTools\SystemTest;

use Composer\Json\JsonManipulator;

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
