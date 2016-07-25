<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Templating\TemplateEngine;

final class TaskRegistryFactory
{
    /**
     * @param Project $project
     * @return TaskRegistry
     */
    public function createWithProject(Project $project)
    {
        return new TaskRegistry($project);
    }
}
