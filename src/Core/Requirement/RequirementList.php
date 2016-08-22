<?php

namespace Ibuildings\QaTools\Core\Requirement;

use ArrayIterator;
use Countable;
use Ibuildings\QaTools\Core\Assert\Assertion;
use IteratorAggregate;

final class RequirementList implements IteratorAggregate, Countable
{
    /**
     * @var Requirement[]
     */
    private $requirements;

    /**
     * @param Requirement[] $requirements
     */
    public function __construct(array $requirements)
    {
        Assertion::allIsInstanceOf($requirements, Requirement::class);

        $this->requirements = $requirements;
    }

    /**
     * @param Requirement $requirement
     * @return RequirementList
     */
    public function add(Requirement $requirement)
    {
        return new RequirementList(array_merge($this->requirements, [$requirement]));
    }

    /**
     * @param callable $predicate
     * @return RequirementList
     */
    public function filter(callable $predicate)
    {
        return new self(array_filter($this->requirements, $predicate));
    }

    /**
     * @param Requirement $requirementToBeFound
     * @return boolean
     */
    public function contains(Requirement $requirementToBeFound)
    {
        /** @var Requirement $requirement */
        foreach ($this->requirements as $requirement) {
            if ($requirement === $requirementToBeFound) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param RequirementList $other
     * @return RequirementList
     */
    public function merge(RequirementList $other)
    {
        return new RequirementList(
            array_merge(
                $this->requirements,
                array_filter(
                    $other->requirements,
                    function (Requirement $requirement) {
                        return !$this->contains($requirement);
                    }
                )
            )
        );
    }

    /**
     * @param RequirementList $other
     * @return bool
     */
    public function equals(RequirementList $other)
    {
        if (count($this->requirements) !== count($other->requirements)) {
            return false;
        }

        foreach ($this->requirements as $i => $requirement) {
            if (!$other->requirements[$i] === $requirement) {
                return false;
            }
        }

        return true;
    }

    public function count()
    {
        return count($this->requirements);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->requirements);
    }
}
