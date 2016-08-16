<?php

namespace Ibuildings\QaTools\Core\Task;

use Ibuildings\QaTools\Core\Task\Composer\Package;
use Ibuildings\QaTools\Core\Task\Composer\PackageSet;

final class RequireComposerPackagesTask implements Task
{
    /**
     * @var PackageSet
     */
    private $packages;

    public function __construct(Package ...$packages)
    {
        $this->packages = new PackageSet($packages);
    }

    public function equals(Task $other)
    {
        /** @var RequireComposerPackagesTask $other */
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
        return sprintf('RequireComposerPackagesTask(%s)', $this->packages);
    }
}
