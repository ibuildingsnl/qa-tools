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
     * @param string      $toolClassName
     * @return void
     */
    public function registerRequirement(Requirement $requirement, $toolClassName);

    /**
     * @param Specification $requirementSpecification
     * @return RequirementList
     */
    public function matchRequirements(Specification $requirementSpecification);

    /**
     * @return Project
     */
    public function getProject();
}
