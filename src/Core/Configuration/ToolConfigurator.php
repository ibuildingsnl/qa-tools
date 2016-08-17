<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Configurator\Configurator;
use Ibuildings\QaTools\Core\Configurator\ConfiguratorList;
use Ibuildings\QaTools\Core\Interviewer\ScopedInterviewer;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ToolConfigurator
{
    /**
     * @var RequirementHelperSet
     */
    private $requirementHelperSet;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(RequirementHelperSet $requirementHelperSet, ContainerInterface $container)
    {
        $this->requirementHelperSet = $requirementHelperSet;
        $this->container = $container;
    }

    public function configure(
        ConfiguratorList $configurators,
        ScopedInterviewer $interviewer,
        RequirementDirectory $requirementDirectory
    ) {
        foreach ($configurators as $configurator) {
            /** @var Configurator $configurator */
            $interviewer->setScope($configurator->getToolClassName());

            $templatePath = $this->container->getParameter(
                sprintf('tool.%s.resource_path', $configurator->getToolClassName())
            );
            $this->requirementHelperSet->setTemplatePath($templatePath);

            $configurator->configure($interviewer, $requirementDirectory, $this->requirementHelperSet);
        }
    }
}
