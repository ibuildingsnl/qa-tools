<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Configurator\ConfiguratorRepository;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Project\Directory;
use Ibuildings\QaTools\Core\Task\Executor\ExecutorExecutor as TasksExecutorExecutor;

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
     * @var TaskDirectoryFactory
     */
    private $taskDirectoryFactory;

    /**
     * @var TasksExecutorExecutor
     */
    private $tasksExecutorExecutor;

    public function __construct(
        ConfigurationRepository $configurationRepository,
        ProjectConfigurator $projectConfigurator,
        ToolConfigurator $toolConfigurator,
        ConfiguratorRepository $configuratorRepository,
        TaskDirectoryFactory $taskDirectoryFactory,
        TasksExecutorExecutor $tasksExecutorExecutor
    ) {
        $this->configurationRepository = $configurationRepository;
        $this->projectConfigurator = $projectConfigurator;
        $this->configuratorRepository = $configuratorRepository;
        $this->taskDirectoryFactory = $taskDirectoryFactory;
        $this->toolConfigurator = $toolConfigurator;
        $this->tasksExecutorExecutor = $tasksExecutorExecutor;
    }

    /**
     * @param Interviewer $interviewer
     * @param Directory   $projectDirectory
     * @return void
     */
    public function configureProject(Interviewer $interviewer, Directory $projectDirectory)
    {
        if ($this->configurationRepository->configurationExists()) {
            $configuration = $this->configurationRepository->load();
        } else {
            $configuration = Configuration::create();
        }

        $memorizingInterviewer = new MemorizingInterviewer($interviewer, $configuration);

        $this->projectConfigurator->configure($memorizingInterviewer, $configuration, $projectDirectory);
        $taskDirectory = $this->taskDirectoryFactory->createWithProject($configuration->getProject());

        $configurators = $this->configuratorRepository->getConfiguratorsForProject($configuration->getProject());
        $this->toolConfigurator->configure($configurators, $memorizingInterviewer, $taskDirectory);

        $this->tasksExecutorExecutor->execute($taskDirectory, $memorizingInterviewer);

        $this->configurationRepository->save($configuration);
    }
}
