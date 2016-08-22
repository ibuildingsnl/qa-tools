<?php

namespace Ibuildings\QaTools\Core\Requirement;

use Ibuildings\QaTools\Core\Composer\Package;

final class ComposerDevDependencyRequirement implements Requirement
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
        return sprintf('ComposerDevDependencyRequirement(%s)', $this->package);
    }
}
