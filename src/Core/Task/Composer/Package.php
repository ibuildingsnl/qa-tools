<?php

namespace Ibuildings\QaTools\Core\Task\Composer;

use Composer\Semver\VersionParser;
use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Exception\InvalidArgumentException;
use UnexpectedValueException;

final class Package
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $versionConstraint;

    /**
     * @param string $name
     * @param string $versionConstraint
     */
    public function __construct($name, $versionConstraint)
    {
        Assertion::string($name, 'Package name ought to be a string, got "%s" of type "%s"');
        Assertion::regex(
            $name,
            '{^[A-Za-z0-9_./-]+$}',
            'Package name "%s" is invalid, it may only consist of [A-Za-z0-9_./-]'
        );
        Assertion::string($versionConstraint, 'Package version constraint ought to be a string, got "%s" of type "%s"');

        try {
            $versionConstraint = (string) (new VersionParser())->parseConstraints($versionConstraint);
        } catch (UnexpectedValueException $e) {
            throw new InvalidArgumentException(
                sprintf('Package version constraint "%s" is invalid', $versionConstraint)
            );
        }

        $this->name = $name;
        $this->versionConstraint = $versionConstraint;
    }

    /**
     * @param string $versionConstraint
     * @return bool
     */
    public function versionConstraintEquals($versionConstraint)
    {
        Assertion::string($versionConstraint, 'Package version constraint ought to be a string, got "%s" of type "%s"');

        try {
            $versionConstraint = (string) (new VersionParser())->parseConstraints($versionConstraint);
        } catch (UnexpectedValueException $e) {
            throw new InvalidArgumentException(
                sprintf('Package version constraint "%s" is invalid', $versionConstraint)
            );
        }

        return $this->versionConstraint === $versionConstraint;
    }

    /**
     * @param Package $other
     * @return bool
     */
    public function equals(Package $other)
    {
        return $this->name === $other->name
            && $this->versionConstraint === $other->versionConstraint;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getVersionConstraint()
    {
        return $this->versionConstraint;
    }

    public function __toString()
    {
        return sprintf('Package("%s:%s")', $this->name, $this->versionConstraint);
    }
}
