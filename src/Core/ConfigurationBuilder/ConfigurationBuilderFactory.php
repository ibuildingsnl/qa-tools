<?php

namespace Ibuildings\QaTools\Core\ConfigurationBuilder;

use Ibuildings\QaTools\Core\Project\Project;

final class ConfigurationBuilderFactory
{
    /**
     * @param Project $project
     * @return ConfigurationBuilder
     */
    public function createWithProject(Project $project)
    {
        return new ConfigurationBuilder($project);
    }
}
