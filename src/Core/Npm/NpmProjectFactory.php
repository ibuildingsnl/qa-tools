<?php

namespace Ibuildings\QaTools\Core\Npm;

interface NpmProjectFactory
{
    /**
     * @param string $directory
     * @return NpmProject
     */
    public function forDirectory($directory);
}
