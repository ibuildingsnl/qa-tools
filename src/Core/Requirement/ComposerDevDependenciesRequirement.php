<?php

namespace Ibuildings\QaTools\Core\Requirement;

use Ibuildings\QaTools\Core\Composer\Package;
use Ibuildings\QaTools\Core\Composer\PackageSet;

final class ComposerDevDependenciesRequirement implements Requirement
{
    /**
     * @var PackageSet
     */
    private $packages;

    public function __construct(Package ...$packages)
    {
        $this->packages = new PackageSet($packages);
    }

    public function equals(Requirement $other)
    {
        /** @var ComposerDevDependenciesRequirement $other */
        return get_class($other) === self::class
            && $this->packages->equals($other->packages);
    }

    /**
     * @return PackageSet
     */
    public function getPackages()
    {
        return $this->packages;
    }

    public function __toString()
    {
        return sprintf('ComposerDevDependenciesRequirement(%s)', $this->packages);
    }
}
