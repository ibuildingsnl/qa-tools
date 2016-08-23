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
     * Backs up the current Composer configuration for later restoration.
     *
     * @return void
     */
    public function backUpConfiguration();

    /**
     * Restores the backed up Composer configuration and installs the locked
     * dependencies. The backup is not cleared after restoration.
     *
     * @return void
     */
    public function restoreConfiguration();
}
