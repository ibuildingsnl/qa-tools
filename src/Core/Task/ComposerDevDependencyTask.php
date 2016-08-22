<?php

namespace Ibuildings\QaTools\Core\Task;

use Ibuildings\QaTools\Core\Composer\Package;

final class ComposerDevDependencyTask implements Task
{
    /**
     * @var Package
     */
    private $package;

    public function __construct(Package $package)
    {
        $this->package = $package;
    }

    /**
     * @return Package
     */
    public function getPackage()
    {
        return $this->package;
    }

    public function __toString()
    {
        return sprintf('ComposerDevDependencyTask(%s)', $this->package);
    }
}
