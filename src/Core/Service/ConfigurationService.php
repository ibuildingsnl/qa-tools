<?php

namespace Ibuildings\QaTools\Core\Service;

use Ibuildings\QaTools\Core\Configuration\Configuration;
use Ibuildings\QaTools\Core\Configuration\TaskHelperSet;
use Ibuildings\QaTools\Core\Configuration\TaskRegistryFactory;
use Ibuildings\QaTools\Core\Configuration\ConfigurationDumper;
use Ibuildings\QaTools\Core\Configuration\ConfigurationLoader;
use Ibuildings\QaTools\Core\Configurator\Configurator;
use Ibuildings\QaTools\Core\Configurator\ConfiguratorRegistry;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Interviewer\MemorizingInterviewer;
use Ibuildings\QaTools\Core\IO\Cli\InterviewerFactory;
use Ibuildings\QaTools\Core\Project\ProjectConfigurator;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ConfigurationService
{
    /**
     * @var ConfigurationLoader
     */
    private $configurationLoader;

    /**
     * @var ProjectConfigurator
     */
    private $projectConfigurator;

    /**
     * @var ConfiguratorRegistry
     */
    private $configuratorRegistry;

    /**
     * @var InterviewerFactory
     */
    private $interviewerFactory;

    /**
     * @var TaskRegistryFactory
     */
    private $taskRegistryFactory;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ConfigurationDumper
     */
    private $configurationDumper;

    /**
     * @var TaskHelperSet
     */
    private $taskHelperSet;

    public function __construct(
        ConfigurationLoader $configurationLoader,
        ProjectConfigurator $projectConfigurator,
        ConfiguratorRegistry $configuratorRegistry,
        InterviewerFactory $interviewerFactory,
        TaskRegistryFactory $taskRegistryFactory,
        TaskHelperSet $taskHelperSet,
        ContainerInterface $container,
        ConfigurationDumper $configurationDumper
    ) {
        $this->configurationLoader         = $configurationLoader;
        $this->projectConfigurator         = $projectConfigurator;
        $this->configuratorRegistry        = $configuratorRegistry;
        $this->interviewerFactory          = $interviewerFactory;
        $this->taskRegistryFactory         = $taskRegistryFactory;
        $this->taskHelperSet               = $taskHelperSet;
        $this->container                   = $container;
        $this->configurationDumper         = $configurationDumper;
    }

    /**
     * @param Interviewer $interviewer
     * @return void
     */
    public function configureProject(Interviewer $interviewer)
    {
        $previousAnswers = [];
        if ($this->configurationLoader->configurationExists()) {
            $previousAnswers = $this->configurationLoader->load()->getAnswers();
        }

        $interviewer = new MemorizingInterviewer($interviewer, $previousAnswers);

        $project = $this->projectConfigurator->configure($interviewer);
        $taskRegistry = $this->taskRegistryFactory->createWithProject($project);

        $runList = $this->configuratorRegistry->getRunListForProjectTypes($project->getProjectTypes());
        foreach ($runList as $configurator) {
            /** @var Configurator $configurator */
            $interviewer->setScope($configurator->getToolClassName());

            $templatePath = $this->container->getParameter(
                sprintf('tool.%s.resource_path', $configurator->getToolClassName())
            );
            $this->taskHelperSet->setTemplatePath($templatePath);

            $configurator->configure($interviewer, $taskRegistry, $this->taskHelperSet);
        }

        $configuration = new Configuration($project, $interviewer->getGivenAnswers());
        $this->configurationDumper->dump($configuration);
    }
}
