<?php

namespace Ibuildings\QaTools\Core\Service;

use Ibuildings\QaTools\Core\Configuration\Configuration;
use Ibuildings\QaTools\Core\Configuration\ConfigurationRepository;
use Ibuildings\QaTools\Core\Configuration\ProjectConfigurator;
use Ibuildings\QaTools\Core\Configuration\RunListConfigurator;
use Ibuildings\QaTools\Core\Configuration\TaskRegistryFactory;
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
     * @var TaskRegistryFactory
     */
    private $taskRegistryFactory;

    public function __construct(
        ConfigurationRepository $configurationRepository,
        ProjectConfigurator $projectConfigurator,
        RunListConfigurator $runListConfigurator,
        ConfiguratorRepository $configuratorRepository,
        TaskRegistryFactory $taskRegistryFactory
    ) {
        $this->configurationRepository     = $configurationRepository;
        $this->projectConfigurator         = $projectConfigurator;
        $this->configuratorRepository      = $configuratorRepository;
        $this->taskRegistryFactory         = $taskRegistryFactory;
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
        $taskRegistry = $this->taskRegistryFactory->createWithProject($configuration->getProject());

        $runList = $this->configuratorRepository->getRunListForProject($configuration->getProject());
        $this->runListConfigurator->configure($runList, $interviewer, $taskRegistry);

        $this->configurationRepository->save($configuration);
    }
}
