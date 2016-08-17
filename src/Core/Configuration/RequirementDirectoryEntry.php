<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Requirement\Requirement;
use Ibuildings\QaTools\Core\Requirement\Specification\Specification;

final class RequirementDirectoryEntry
{
    /**
     * @var Requirement
     */
    private $requirement;

    /**
     * @var string
     */
    private $registeredByTool;

    /**
     * @param Requirement $requirement
     * @param string      $registeredByTool
     */
    public function __construct(Requirement $requirement, $registeredByTool)
    {
        Assertion::string($registeredByTool, 'Tool ought to be a tool class name, got "%s" of type "%s"');

        $this->requirement = $requirement;
        $this->registeredByTool = $registeredByTool;
    }

    /**
     * @param Specification $specification
     * @return bool
     */
    public function requirementSatisfies(Specification $specification)
    {
        return $specification->isSatisfiedBy($this->requirement);
    }

    /**
     * @param string $toolClassName
     * @return bool
     */
    public function wasRegisteredByTool($toolClassName)
    {
        Assertion::string($toolClassName, 'Tool class name ought to be a string, got "%s" of type "%s"');

        return $this->registeredByTool === $toolClassName;
    }

    /**
     * @return Requirement
     */
    public function getRequirement()
    {
        return $this->requirement;
    }

    public function __toString()
    {
        return sprintf('RequirementDirectoryEntry(requirement=%s, tool=%s)');
    }
}
