<?php

namespace Ibuildings\QaTools\Core\Composer;

interface ProjectFactory
{
    /**
     * @param string $directory
     * @return Project
     */
    public function forDirectory($directory);
}
