<?php

namespace Ibuildings\QaTools\Core\Service;

use Ibuildings\QaTools\Core\Configuration\Configuration;
use Ibuildings\QaTools\Core\Configuration\TaskHelperSet;
use Ibuildings\QaTools\Core\Configuration\TaskRegistryFactory;
use Ibuildings\QaTools\Core\Configuration\ConfigurationDumper;
use Ibuildings\QaTools\Core\Configuration\ConfigurationLoader;
use Ibuildings\QaTools\Core\Configurator\Configurator;
use Ibuildings\QaTools\Core\Configurator\RunList;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
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
     * @var RunList
     */
    private $runList;

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
        RunList $runList,
        InterviewerFactory $interviewerFactory,
        TaskRegistryFactory $taskRegistryFactory,
        TaskHelperSet $taskHelperSet,
        ContainerInterface $container,
        ConfigurationDumper $configurationDumper
    ) {
        $this->configurationLoader         = $configurationLoader;
        $this->projectConfigurator         = $projectConfigurator;
        $this->runList                     = $runList;
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
        $configurationLoader = $this->configurationLoader;

        if ($configurationLoader->configurationExists()) {
            $configuration = $configurationLoader->load();
            $previousAnswers = $configuration->getAnswers();
        } else {
            $previousAnswers = [];
        }

        $interviewer = $this->interviewerFactory->createMemorizingWith($interviewer, $previousAnswers);

        $projectConfigurator = $this->projectConfigurator;
        $runList             = $this->runList;

        $project = $projectConfigurator->configure($interviewer);

        $taskRegistry = $this->taskRegistryFactory->createWithProject($project);

        /** @var Configurator $configurator */
        foreach ($runList->getConfiguratorsForProjectTypes($project->getProjectTypes()) as $configurator) {
            $interviewer->setScope($configurator->getToolClassName());

            $templatePath = $this->container->getParameter(sprintf('tool.%s.resource_path', $configurator->getToolClassName()));
            $this->taskHelperSet->setTemplatePath($templatePath);

            $configurator->configure($interviewer, $taskRegistry, $this->taskHelperSet);
        }

        $configuration = new Configuration($project, $interviewer->getGivenAnswers());
        $this->configurationDumper->dump($configuration);
    }
}
