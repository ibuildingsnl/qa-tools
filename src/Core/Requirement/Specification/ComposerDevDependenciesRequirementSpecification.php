<?php

namespace Ibuildings\QaTools\Core\Requirement\Specification;

use Ibuildings\QaTools\Core\Composer\Package;
use Ibuildings\QaTools\Core\Composer\PackageName;
use Ibuildings\QaTools\Core\Requirement\ComposerDevDependencyRequirement;
use Ibuildings\QaTools\Core\Requirement\Requirement;

final class ComposerDevDependenciesRequirementSpecification implements Specification
{
    /**
     * @var PackageName
     */
    private $packageName;

    /**
     * @param PackageName $packageName
     * @return ComposerDevDependenciesRequirementSpecification
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

    public function isSatisfiedBy(Requirement $requirement)
    {
        /** @var ComposerDevDependencyRequirement $requirement */
        return get_class($requirement) === ComposerDevDependencyRequirement::class
            && $requirement->getPackage()->getName()->equals($this->packageName);
    }

    public function equals(Specification $specification)
    {
        /** @var self $specification */
        return get_class($specification) === self::class
            && $this->packageName->equals($specification->packageName);
    }

    public function __toString()
    {
        return sprintf(
            'ComposerDevDependenciesRequirementSpecification("%s:*")',
            $this->packageName->getName()
        );
    }
}
