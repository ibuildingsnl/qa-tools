<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Project\Project;

interface RequirementDirectoryFactory
{
    /**
     * @param Project $project
     * @return RequirementDirectory
     */
    public function createWithProject(Project $project);
}
