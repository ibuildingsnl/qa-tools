<?php

namespace Ibuildings\QaTools\SystemTest;

use Ibuildings\QaTools\Core\Composer\CliComposerProject;
use Ibuildings\QaTools\Core\Composer\Package;
use Ibuildings\QaTools\Core\Composer\PackageName;
use Ibuildings\QaTools\Core\Project\Directory;

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
        self::composer()->initialise($packageName);
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
        return new CliComposerProject(getcwd());
    }
}
