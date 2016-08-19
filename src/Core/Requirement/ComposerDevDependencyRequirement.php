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

    public function equals(Requirement $other)
    {
        /** @var ComposerDevDependencyRequirement $other */
        return get_class($other) === self::class
            && $this->package->equals($other->package);
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
