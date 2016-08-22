<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Configurator\Configurator;
use Ibuildings\QaTools\Core\Configurator\ConfiguratorList;
use Ibuildings\QaTools\Core\Interviewer\ScopedInterviewer;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ToolConfigurator
{
    /**
     * @var TaskHelperSet
     */
    private $taskHelperSet;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(TaskHelperSet $taskHelperSet, ContainerInterface $container)
    {
        $this->taskHelperSet = $taskHelperSet;
        $this->container = $container;
    }

    public function configure(
        ConfiguratorList $configurators,
        ScopedInterviewer $interviewer,
        TaskDirectory $taskDirectory
    ) {
        foreach ($configurators as $configurator) {
            /** @var Configurator $configurator */
            $interviewer->setScope($configurator->getToolClassName());

            $templatePath = $this->container->getParameter(
                sprintf('tool.%s.resource_path', $configurator->getToolClassName())
            );
            $this->taskHelperSet->setTemplatePath($templatePath);

            $configurator->configure($interviewer, $taskDirectory, $this->taskHelperSet);
        }
    }
}
