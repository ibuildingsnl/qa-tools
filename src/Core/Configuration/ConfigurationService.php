<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Configurator\ConfiguratorRepository;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Project\Directory;
use Ibuildings\QaTools\Core\Requirement\Executor\ExecutorExecutor as RequirementsExecutorExecutor;
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
     * @var RequirementsExecutorExecutor
     */
    private $requirementsExecutorExecutor;

    public function __construct(
        ConfigurationRepository $configurationRepository,
        ProjectConfigurator $projectConfigurator,
        ToolConfigurator $toolConfigurator,
        ConfiguratorRepository $configuratorRepository,
        RequirementDirectoryFactory $requirementDirectoryFactory,
        RequirementsExecutorExecutor $requirementsExecutorExecutor
    ) {
        $this->configurationRepository = $configurationRepository;
        $this->projectConfigurator = $projectConfigurator;
        $this->configuratorRepository = $configuratorRepository;
        $this->requirementDirectoryFactory = $requirementDirectoryFactory;
        $this->toolConfigurator = $toolConfigurator;
        $this->requirementsExecutorExecutor = $requirementsExecutorExecutor;
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
        $requirementDirectory = $this->requirementDirectoryFactory->createWithProject($configuration->getProject());

        $configurators = $this->configuratorRepository->getConfiguratorsForProject($configuration->getProject());
        $this->toolConfigurator->configure($configurators, $memorizingInterviewer, $requirementDirectory);

        $this->requirementsExecutorExecutor->execute($requirementDirectory, $memorizingInterviewer);

        $this->configurationRepository->save($configuration);
    }
}
