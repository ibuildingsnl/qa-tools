<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Templating\TemplateEngine;

final class TaskDirectoryFactory
{
    /**
     * @param Project $project
     * @return TaskDirectory
     */
    public function createWithProject(Project $project)
    {
        return new TaskDirectory($project);
    }
}
