<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Requirement\Requirement;
use Ibuildings\QaTools\Core\Requirement\Specification\Specification;
use Ibuildings\QaTools\Core\Requirement\RequirementList;

final class InMemoryRequirementDirectory implements RequirementDirectory
{
    /**
     * @var Project
     */
    private $project;

    /**
     * @var RequirementDirectoryEntry[]
     */
    private $entries;

    public function __construct(Project $project)
    {
        $this->project = $project;
        $this->entries = [];
    }

    public function registerRequirement(Requirement $requirement, $toolClassName)
    {
        $this->entries[] = new RequirementDirectoryEntry($requirement, $toolClassName);
    }

    public function matchRequirements(Specification $specification)
    {
        $matchingRequirements = [];
        foreach ($this->entries as $entry) {
            if ($entry->requirementSatisfies($specification)) {
                $matchingRequirements[] = $entry->getRequirement();
            }
        }

        return new RequirementList($matchingRequirements);
    }

    public function getProject()
    {
        return $this->project;
    }
}
