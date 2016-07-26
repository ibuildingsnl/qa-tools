<?php

namespace Ibuildings\QaTools\Core\Service;

use Ibuildings\QaTools\Core\Configuration\Configuration;
use Ibuildings\QaTools\Core\Configuration\ConfigurationDumper;
use Ibuildings\QaTools\Core\Configuration\ConfigurationLoader;
use Ibuildings\QaTools\Core\Configuration\ProjectConfigurator;
use Ibuildings\QaTools\Core\Configuration\RunListConfigurator;
use Ibuildings\QaTools\Core\Configuration\TaskRegistryFactory;
use Ibuildings\QaTools\Core\Configurator\ConfiguratorRegistry;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Interviewer\MemorizingInterviewer;

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
     * @var RunListConfigurator
     */
    private $runListConfigurator;

    /**
     * @var ConfiguratorRegistry
     */
    private $configuratorRegistry;

    /**
     * @var TaskRegistryFactory
     */
    private $taskRegistryFactory;

    /**
     * @var ConfigurationDumper
     */
    private $configurationDumper;

    public function __construct(
        ConfigurationLoader $configurationLoader,
        ProjectConfigurator $projectConfigurator,
        RunListConfigurator $runListConfigurator,
        ConfiguratorRegistry $configuratorRegistry,
        TaskRegistryFactory $taskRegistryFactory,
        ConfigurationDumper $configurationDumper
    ) {
        $this->configurationLoader         = $configurationLoader;
        $this->projectConfigurator         = $projectConfigurator;
        $this->configuratorRegistry        = $configuratorRegistry;
        $this->taskRegistryFactory         = $taskRegistryFactory;
        $this->configurationDumper         = $configurationDumper;
        $this->runListConfigurator         = $runListConfigurator;
    }

    /**
     * @param Interviewer $interviewer
     * @return void
     */
    public function configureProject(Interviewer $interviewer)
    {
        if ($this->configurationLoader->configurationExists()) {
            $configuration = $this->configurationLoader->load();
        } else {
            $configuration = Configuration::create();
        }

        $interviewer = new MemorizingInterviewer($interviewer, $configuration);

        $this->projectConfigurator->configure($interviewer, $configuration);
        $taskRegistry = $this->taskRegistryFactory->createWithProject($configuration->getProject());

        $runList = $this->configuratorRegistry->getRunListForProject($configuration->getProject());
        $this->runListConfigurator->configure($runList, $interviewer, $taskRegistry);

        $this->configurationDumper->dump($configuration);
    }
}
