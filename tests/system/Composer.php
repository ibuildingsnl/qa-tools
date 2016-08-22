<?php

namespace Ibuildings\QaTools\SystemTest;

use Composer\Json\JsonManipulator;
use Ibuildings\QaTools\Core\Composer\Package;
use Ibuildings\QaTools\Core\Composer\PackageName;

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
     * @param Package $package
     * @return void
     */
    public static function addConflict(Package $package)
    {
        $packageName = $package->getName()->getName();
        $versionConstraint = $package->getVersionConstraint()->getConstraint();

        $manipulator = new JsonManipulator(file_get_contents('composer.json'));
        $manipulator->addSubNode('conflict', $packageName, $versionConstraint);

        file_put_contents('composer.json', $manipulator->getContents());
    }

    public static function assertPackageIsInstalled(PackageName $packageName)
    {
        assertFileExists(sprintf('vendor/%s/composer.json', $packageName->getName()));
    }

    public static function assertPackageIsNotInstalled(PackageName $packageName)
    {
        assertFileNotExists(sprintf('vendor/%s', $packageName->getName()));
    }
}
