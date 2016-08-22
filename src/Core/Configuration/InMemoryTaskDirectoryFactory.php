<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Project\Project;

final class InMemoryTaskDirectoryFactory implements TaskDirectoryFactory
{
    public function createWithProject(Project $project)
    {
        return new InMemoryTaskDirectory($project);
    }
}
