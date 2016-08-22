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
     * @var RequirementList
     */
    private $requirements;

    public function __construct(Project $project)
    {
        $this->project = $project;
        $this->requirements = new RequirementList();
    }

    public function registerRequirement(Requirement $requirement)
    {
        $this->requirements = $this->requirements->add($requirement);
    }

    /**
     * @param callable $predicate
     * @return RequirementList
     */
    public function filterRequirements(callable $predicate)
    {
        return $this->requirements->filter($predicate);
    }

    /**
     * @return RequirementList
     */
    public function getRequirements()
    {
        return $this->requirements;
    }

    public function getProject()
    {
        return $this->project;
    }
}
