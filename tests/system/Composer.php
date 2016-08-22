<?php

namespace Ibuildings\QaTools\SystemTest;

use Ibuildings\QaTools\Core\Composer\CliComposerProject;
use Ibuildings\QaTools\Core\Composer\Package;
use Ibuildings\QaTools\Core\Composer\PackageName;

final class Composer
{
    /**
     * Initialise a new Composer project.
     *
     * @param PackageName $packageName
     * @return void
     */
    public static function initialise(PackageName $packageName)
    {
        $composer = self::composer();
        $composer->initialise($packageName);

        // Emulate all the tools' Composer packages locally to guarantee test
        // reliability by removing the Internet factor and to speed up tests.
        $configuration = json_decode(file_get_contents('composer.json'));
        $configuration->repositories = [
            ['packagist' => false],
            ['type' => 'path', 'url' => __DIR__ . '/../composer/packages/phpmd'],
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
        self::composer()->addConflict($package);
    }

    public static function assertPackageIsInstalled(PackageName $packageName)
    {
        assertFileExists(sprintf('vendor/%s/composer.json', $packageName->getName()));
    }

    public static function assertPackageIsNotInstalled(PackageName $packageName)
    {
        assertFileNotExists(sprintf('vendor/%s', $packageName->getName()));
    }

    /**
     * @return CliComposerProject
     */
    private static function composer()
    {
        return new CliComposerProject(getcwd(), __DIR__ . '/../../vendor/bin/composer');
    }
}
