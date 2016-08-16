<?php

namespace Ibuildings\QaTools\Core\Task\Specification;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Task\Composer\Package;
use Ibuildings\QaTools\Core\Task\RequireComposerPackagesTask;
use Ibuildings\QaTools\Core\Task\Task;

final class ComposerPackageIsRequiredSpecification implements Specification
{
    /**
     * @var string
     */
    private $packageName;

    /**
     * @param string $packageName
     * @return ComposerPackageIsRequiredSpecification
     */
    public static function ofAnyVersion($packageName)
    {
        Assertion::string($packageName, 'Package name ought to be a string, got "%s" of type "%s"');

        return new self($packageName);
    }

    /**
     * @param string $packageName
     */
    private function __construct($packageName)
    {
        $this->packageName = $packageName;
    }

    public function isSatisfiedBy(Task $task)
    {
        /** @var RequireComposerPackagesTask $task */
        return get_class($task) === RequireComposerPackagesTask::class
            && $task->getPackages()->filter(function (Package $package) {
                return $package->getName() === $this->packageName;
            });
    }

    public function __toString()
    {
        return sprintf(
            'ComposerPackageIsRequiredSpecification("%s:*")',
            $this->packageName
        );
    }
}
