<?php

namespace Ibuildings\QaTools\Core\Service;

use Ibuildings\QaTools\Core\Configuration\Configuration;
use Ibuildings\QaTools\Core\Configuration\ConfigurationRepository;
use Ibuildings\QaTools\Core\Configuration\FileConfigurationRepository;
use Ibuildings\QaTools\Core\Configuration\ProjectConfigurator;
use Ibuildings\QaTools\Core\Configuration\RunListConfigurator;
use Ibuildings\QaTools\Core\Configuration\TaskDirectoryFactory;
use Ibuildings\QaTools\Core\Configurator\ConfiguratorRepository;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Interviewer\MemorizingInterviewer;

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
     * @var RunListConfigurator
     */
    private $runListConfigurator;

    /**
     * @var ConfiguratorRepository
     */
    private $configuratorRepository;

    /**
     * @var TaskDirectoryFactory
     */
    private $taskDirectoryFactory;

    public function __construct(
        ConfigurationRepository $configurationRepository,
        ProjectConfigurator $projectConfigurator,
        RunListConfigurator $runListConfigurator,
        ConfiguratorRepository $configuratorRepository,
        TaskDirectoryFactory $taskDirectoryFactory
    ) {
        $this->configurationRepository     = $configurationRepository;
        $this->projectConfigurator         = $projectConfigurator;
        $this->configuratorRepository      = $configuratorRepository;
        $this->taskDirectoryFactory         = $taskDirectoryFactory;
        $this->runListConfigurator         = $runListConfigurator;
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

        $interviewer = new MemorizingInterviewer($interviewer, $configuration);

        $this->projectConfigurator->configure($interviewer, $configuration);
        $taskDirectory = $this->taskDirectoryFactory->createWithProject($configuration->getProject());

        $runList = $this->configuratorRepository->getRunListForProject($configuration->getProject());
        $this->runListConfigurator->configure($runList, $interviewer, $taskDirectory);

        // Execute tasks from task directory

        $this->configurationRepository->save($configuration);
    }
}
