<?php

namespace Ibuildings\QaTools\Core\Composer;

use ArrayIterator;
use Countable;
use IteratorAggregate;

final class PackageSet implements Countable, IteratorAggregate
{
    /**
     * @var Package[]
     */
    private $packages = [];

    /**
     * @param Package[] $packages
     */
    public function __construct(array $packages = [])
    {
        foreach ($packages as $package) {
            $this->initializeWith($package);
        }
    }

    /**
     * @param Package $package
     * @return PackageSet
     */
    public function add(Package $package)
    {
        return new PackageSet(array_merge($this->packages, [$package]));
    }

    /**
     * @param Package $package
     * @return boolean
     */
    public function contains(Package $package)
    {
        foreach ($this->packages as $existingPackage) {
            if ($package->equals($existingPackage)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param callable $predicate
     * @return PackageSet
     */
    public function filter(callable $predicate)
    {
        return new PackageSet(array_filter($this->packages, $predicate));
    }

    /**
     * @param PackageSet $other
     * @return bool
     */
    public function equals(PackageSet $other)
    {
        if (count($this->packages) !== count($other->packages)) {
            return false;
        }

        foreach ($this->packages as $package) {
            if (!$other->contains($package)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns an array of package descriptors (eg. "phpmd/phpmd:^2.0").
     *
     * @return string[]
     */
    public function getDescriptors()
    {
        return array_map(
            function (Package $package) {
                return $package->getDescriptor();
            },
            $this->packages
        );
    }

    /**
     * @param Package $package
     */
    private function initializeWith(Package $package)
    {
        if ($this->contains($package)) {
            return;
        }

        $this->packages[] = $package;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->packages);
    }

    public function count()
    {
        return count($this->packages);
    }

    public function __toString()
    {
        return sprintf('PackageSet[%d](%s)', count($this->packages), join(', ', array_map('strval', $this->packages)));
    }
}
