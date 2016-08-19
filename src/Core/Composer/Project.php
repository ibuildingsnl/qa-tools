<?php

namespace Ibuildings\QaTools\Core\Composer;

interface Project
{
    /**
     * Initialise a new Composer project.
     *
     * @param PackageName $packageName
     * @return void
     */
    public function initialise(PackageName $packageName);

    /**
     * Verifies that installing the given set of packages as development dependencies
     * won't cause any conflicts.
     *
     * @param PackageSet $packages
     * @return void
     */
    public function verifyDevDependenciesWouldntConflict(PackageSet $packages);

    /**
     * Add and install the set of packages as development dependencies.
     *
     * @param PackageSet $packages
     * @return void
     */
    public function requireDevDependencies(PackageSet $packages);

    /**
     * Install the locked dependencies.
     *
     * @return void
     */
    public function install();

    /**
     * Adds a dependency conflict.
     *
     * @param Package $package
     * @return void
     */
    public function addConflict(Package $package);

    /**
     * Returns the current Composer configuration.
     *
     * @return Configuration
     */
    public function getConfiguration();

    /**
     * @param Configuration $configuration
     * @return
     */
    public function verifyConfigurationCanBeRestored(Configuration $configuration);

    /**
     * Restores a Composer configuration and installs the locked dependencies.
     *
     * @param Configuration $configuration
     * @return void
     */
    public function restoreConfiguration(Configuration $configuration);
}
