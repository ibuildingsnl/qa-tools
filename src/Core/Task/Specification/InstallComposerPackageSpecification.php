<?php

namespace Ibuildings\QaTools\Core\Task\Specification;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Composer\Package;
use Ibuildings\QaTools\Core\Composer\PackageName;
use Ibuildings\QaTools\Core\Task\InstallComposerPackagesTask;
use Ibuildings\QaTools\Core\Task\Task;

final class InstallComposerPackageSpecification implements Specification
{
    /**
     * @var PackageName
     */
    private $packageName;

    /**
     * @param PackageName $packageName
     * @return InstallComposerPackageSpecification
     */
    public static function ofAnyVersion(PackageName $packageName)
    {
        return new self($packageName);
    }

    /**
     * @param PackageName $packageName
     */
    private function __construct($packageName)
    {
        $this->packageName = $packageName;
    }

    public function isSatisfiedBy(Task $task)
    {
        /** @var InstallComposerPackagesTask $task */
        return get_class($task) === InstallComposerPackagesTask::class
            && $task->getPackages()->filter(function (Package $package) {
                return $package->getName()->equals($this->packageName);
            });
    }

    public function __toString()
    {
        return sprintf(
            'InstallComposerPackageSpecification("%s:*")',
            $this->packageName->getName()
        );
    }
}
