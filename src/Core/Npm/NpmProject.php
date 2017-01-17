<?php

namespace Ibuildings\QaTools\Core\Npm;

interface NpmProject
{
    /**
     * @return bool
     */
    public function isInitialised();

    public function initialise();

    /**
     * @param array $packages
     * @return bool
     */
    public function verifyDevDependenciesCanBeInstalled(array $packages);

    /**
     * @param array $packages
     */
    public function installDevDependencies(array $packages);
}
