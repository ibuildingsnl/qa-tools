<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Configurator\ConfiguratorRepository;
use Ibuildings\QaTools\Core\Exception\RuntimeException;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Project\Directory;
use Ibuildings\QaTools\Core\Task\Executor\TaskDirectoryExecutor;

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
     * @var TaskDirectoryExecutor
     */
    private $taskDirectoryExecutor;

    public function __construct(
        ConfigurationRepository $configurationRepository,
        ProjectConfigurator $projectConfigurator,
        ToolConfigurator $toolConfigurator,
        ConfiguratorRepository $configuratorRepository,
        TaskDirectoryFactory $taskDirectoryFactory,
        TaskDirectoryExecutor $taskDirectoryExecutor
    ) {
        $this->configurationRepository = $configurationRepository;
        $this->projectConfigurator = $projectConfigurator;
        $this->configuratorRepository = $configuratorRepository;
        $this->taskDirectoryFactory = $taskDirectoryFactory;
        $this->toolConfigurator = $toolConfigurator;
        $this->taskDirectoryExecutor = $taskDirectoryExecutor;
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

        if (!$this->taskDirectoryExecutor->execute($taskDirectory, $memorizingInterviewer)) {
            throw new RuntimeException('Execution failed');
        }

        $this->configurationRepository->save($configuration);
    }
}
