<?php

namespace Ibuildings\QaTools\Core\Project;

use ArrayIterator;
use Countable;
use IteratorAggregate;

final class ProjectTypeSet implements Countable, IteratorAggregate
{
    /**
     * @var ProjectType[]
     */
    private $projectTypes = [];

    /**
     * @param ProjectType[] $projectTypes
     */
    public function __construct(array $projectTypes = [])
    {
        foreach ($projectTypes as $projectType) {
            $this->initializeWith($projectType);
        }
    }

    /**
     * @param ProjectType $projectType The project type to search for.
     * @return boolean TRUE if the collection contains the element, FALSE otherwise.
     */
    public function contains(ProjectType $projectType)
    {
        foreach ($this->projectTypes as $existingProjectType) {
            if ($projectType->equals($existingProjectType)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param ProjectTypeSet $other
     * @return bool
     */
    public function equals(ProjectTypeSet $other)
    {
        if (count($this->projectTypes) !== count($other->projectTypes)) {
            return false;
        }

        foreach ($this->projectTypes as $projectType) {
            if (!$other->contains($projectType)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return ProjectType[]
     */
    public function asArray()
    {
        return $this->projectTypes;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->projectTypes);
    }

    public function count()
    {
        return count($this->projectTypes);
    }

    /**
     * @param ProjectType $projectType
     */
    private function initializeWith(ProjectType $projectType)
    {
        if ($this->contains($projectType)) {
            return;
        }

        $this->projectTypes[] = $projectType;
    }

    public function __toString()
    {
        return sprintf(
            'ProjectTypeSet[%d](%s)',
            count($this->projectTypes),
            join(', ', array_map('strval', $this->projectTypes))
        );
    }
}
