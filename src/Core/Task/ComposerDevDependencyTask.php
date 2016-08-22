<?php

namespace Ibuildings\QaTools\Core\Task;

use Ibuildings\QaTools\Core\Assert\Assertion;

final class ComposerDevDependencyTask implements Task
{
    /** @var string */
    private $packageName;
    /** @var string */
    private $packageVersionConstraint;

    /**
     * @param string $packageName
     * @param string $packageVersionConstraint
     */
    public function __construct($packageName, $packageVersionConstraint)
    {
        Assertion::string($packageName, 'Composer package name ought to be a string, got "%s" of type "%s"');
        Assertion::string(
            $packageVersionConstraint,
            'Composer package version constraint ought to be a string, got "%s" of type "%s"'
        );

        $this->packageName = $packageName;
        $this->packageVersionConstraint = $packageVersionConstraint;
    }

    /**
     * @return string
     */
    public function getPackageName()
    {
        return $this->packageName;
    }

    /**
     * @return string
     */
    public function getPackageVersionConstraint()
    {
        return $this->packageVersionConstraint;
    }

    public function __toString()
    {
        return sprintf('ComposerDevDependencyTask("%s:%s")', $this->packageName, $this->packageVersionConstraint);
    }
}
