<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Project\Project;

final class InMemoryRequirementDirectoryFactory implements RequirementDirectoryFactory
{
    public function createWithProject(Project $project)
    {
        return new InMemoryRequirementDirectory($project);
    }
}
