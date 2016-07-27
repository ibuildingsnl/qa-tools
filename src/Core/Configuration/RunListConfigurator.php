<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Configurator\Configurator;
use Ibuildings\QaTools\Core\Configurator\ConfiguratorList;
use Ibuildings\QaTools\Core\Configuration\MemorizingInterviewer;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class RunListConfigurator
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
        ConfiguratorList $runList,
        MemorizingInterviewer $interviewer,
        TaskDirectory $taskDirectory
    ) {
        foreach ($runList as $configurator) {
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
