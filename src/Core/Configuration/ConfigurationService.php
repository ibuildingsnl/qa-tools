<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Configurator\ConfiguratorRepository;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Task\Compiler\TaskListCompiler;
use Ibuildings\QaTools\Core\Task\Executor\TaskListExecutor;

final class ConfigurationService
{
    /**
     * @var ConfigurationRepository
     */
    private $configurationRepository;

    /**
     * @var ProjectConfigurator
     */
    private $projectConfigurator;

    /**
     * @var ToolConfigurator
     */
    private $toolConfigurator;

    /**
     * @var ConfiguratorRepository
     */
    private $configuratorRepository;

    /**
     * @var RequirementDirectoryFactory
     */
    private $requirementDirectoryFactory;

    /**
     * @var TaskListCompiler
     */
    private $taskListCompiler;

    /**
     * @var TaskListExecutor
     */
    private $taskListExecutor;

    public function __construct(
        ConfigurationRepository $configurationRepository,
        ProjectConfigurator $projectConfigurator,
        ToolConfigurator $toolConfigurator,
        ConfiguratorRepository $configuratorRepository,
        RequirementDirectoryFactory $requirementDirectoryFactory,
        TaskListCompiler $taskListCompiler,
        TaskListExecutor $taskListExecutor
    ) {
        $this->configurationRepository = $configurationRepository;
        $this->projectConfigurator = $projectConfigurator;
        $this->configuratorRepository = $configuratorRepository;
        $this->requirementDirectoryFactory = $requirementDirectoryFactory;
        $this->toolConfigurator = $toolConfigurator;
        $this->taskListCompiler = $taskListCompiler;
        $this->taskListExecutor = $taskListExecutor;
    }

    /**
     * @param Interviewer $interviewer
     * @return void
     */
    public function configureProject(Interviewer $interviewer)
    {
        if ($this->configurationRepository->configurationExists()) {
            $configuration = $this->configurationRepository->load();
        } else {
            $configuration = Configuration::create();
        }

        $memorizingInterviewer = new MemorizingInterviewer($interviewer, $configuration);

        $this->projectConfigurator->configure($memorizingInterviewer, $configuration);
        $requirementDirectory = $this->requirementDirectoryFactory->createWithProject($configuration->getProject());

        $configurators = $this->configuratorRepository->getConfiguratorsForProject($configuration->getProject());
        $this->toolConfigurator->configure($configurators, $memorizingInterviewer, $requirementDirectory);

        $tasks = $this->taskListCompiler->compile($requirementDirectory, $memorizingInterviewer);
        $this->taskListExecutor->execute($tasks, $memorizingInterviewer);

        $this->configurationRepository->save($configuration);
    }
}
