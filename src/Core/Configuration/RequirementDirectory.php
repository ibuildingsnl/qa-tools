<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Requirement\Requirement;
use Ibuildings\QaTools\Core\Requirement\Specification\Specification;
use Ibuildings\QaTools\Core\Requirement\RequirementList;

interface RequirementDirectory
{
    /**
     * @param Requirement $requirement
     * @return void
     */
    public function registerRequirement(Requirement $requirement);

    /**
     * @param callable $predicate
     * @return RequirementList
     */
    public function filterRequirements(callable $predicate);

    /**
     * @return Project
     */
    public function getProject();
}
