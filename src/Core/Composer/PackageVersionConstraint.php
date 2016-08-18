<?php

namespace Ibuildings\QaTools\Core\Composer;

use Composer\Semver\VersionParser;
use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Exception\InvalidArgumentException;
use UnexpectedValueException;

final class PackageVersionConstraint
{
    /**
     * @var string
     */
    private $constraint;

    /**
     * @var string
     */
    private $parsedConstraint;

    /**
     * @param string $constraint
     * @return PackageVersionConstraint
     */
    public static function parse($constraint)
    {
        Assertion::string($constraint, 'Package version constraint ought to be a string, got "%s" of type "%s"');

        try {
            $parsedConstraint = (string) (new VersionParser())->parseConstraints($constraint);
        } catch (UnexpectedValueException $e) {
            throw new InvalidArgumentException(
                sprintf('Package version constraint "%s" is invalid', $constraint)
            );
        }

        return new PackageVersionConstraint($constraint, $parsedConstraint);
    }

    /**
     * @param string $constraint
     * @param string $parsedConstraint
     */
    private function __construct($constraint, $parsedConstraint)
    {
        $this->constraint = $constraint;
        $this->parsedConstraint = $parsedConstraint;
    }

    /**
     * @param PackageVersionConstraint $other
     * @return bool
     */
    public function equals(PackageVersionConstraint $other)
    {
        return $this->parsedConstraint === $other->parsedConstraint;
    }

    /**
     * @return string
     */
    public function getConstraint()
    {
        return $this->constraint;
    }

    public function __toString()
    {
        return sprintf('PackageVersionConstraint("%s", parsed="%s")', $this->constraint, $this->parsedConstraint);
    }
}
