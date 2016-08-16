<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Project\Project;

interface TaskDirectoryFactory
{
    /**
     * @param Project $project
     * @return TaskDirectory
     */
    public function createWithProject(Project $project);
}
