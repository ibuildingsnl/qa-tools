<?php

namespace Ibuildings\QaTools\Core\Composer;

interface Project
{
    /**
     * Verifies that installing the given set of packages as development dependencies
     * won't cause any conflicts.
     *
     * @param PackageSet $packages
     * @return void
     */
    public function verifyDevDependenciesWillNotConflict(PackageSet $packages);

    /**
     * Add and install the set of packages as development dependencies.
     *
     * @param PackageSet $packages
     * @return void
     */
    public function requireDevDependencies(PackageSet $packages);

    /**
     * Returns the current Composer configuration.
     *
     * @return Configuration
     */
    public function getConfiguration();

    /**
     * Restores a Composer configuration and installs the locked dependencies.
     *
     * @param Configuration $configuration
     * @return void
     */
    public function restoreConfiguration(Configuration $configuration);
}
