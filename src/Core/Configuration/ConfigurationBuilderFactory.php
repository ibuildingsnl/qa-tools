<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Templating\TemplateEngine;

final class ConfigurationBuilderFactory
{
    /**
     * @var TemplateEngine
     */
    private $templateEngine;

    public function __construct(TemplateEngine $templateEngine)
    {
        $this->templateEngine = $templateEngine;
    }

    /**
     * @param Project $project
     * @return ConfigurationBuilder
     */
    public function createWithProject(Project $project)
    {
        return new ConfigurationBuilder($this->templateEngine, $project);
    }
}
