<?php

namespace Ibuildings\QaTools\Core\Application\Command;

use Ibuildings\QaTools\Core\Configuration\ConfigurationDumper;
use Ibuildings\QaTools\Core\Configuration\ConfigurationRepository;
use Ibuildings\QaTools\Core\Configuration\ProjectConfigurator;
use Ibuildings\QaTools\Core\Configuration\RunListConfigurator;
use Ibuildings\QaTools\Core\Configuration\TaskRegistryFactory;
use Ibuildings\QaTools\Core\Configurator\ConfiguratorRepository;
use Ibuildings\QaTools\Core\IO\Cli\InterviewerFactory;
use Ibuildings\QaTools\Core\Service\ConfigurationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class ConfigureCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected function configure()
    {
        $this
            ->setName('configure')
            ->setDescription('Configure the Ibuildings QA Tools')
            ->setHelp('Configure the Ibuildings QA Tools');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $service = new ConfigurationService(
            $this->getConfigurationRepository(),
            $this->getProjectConfigurator(),
            $this->getRunListConfigurator(),
            $this->getConfiguratorRegistry(),
            $this->getTaskRegistryFactory()
        );

        $service->configureProject($this->getInterviewerFactory()->createWith($input, $output));
    }

    /**
     * @return InterviewerFactory
     */
    private function getInterviewerFactory()
    {
        return $this->container->get('qa_tools.io.cli.interviewer_factory');
    }

    /**
     * @return ProjectConfigurator
     */
    protected function getProjectConfigurator()
    {
        return $this->container->get('qa_tools.project_configurator');
    }

    /**
     * @return RunListConfigurator|object
     */
    private function getRunListConfigurator()
    {
        return $this->container->get('qa_tools.run_list_configurator');
    }

    /**
     * @return ConfiguratorRepository
     */
    private function getConfiguratorRegistry()
    {
        return $this->container->get('qa_tools.configurator_repository');
    }

    /**
     * @return TaskRegistryFactory
     */
    protected function getTaskRegistryFactory()
    {
        return $this->container->get('qa_tools.configuration.task_registry.factory');
    }

    /**
     * @return ConfigurationRepository
     */
    private function getConfigurationRepository()
    {
        return $this->container->get('qa_tools.configuration.configuration_repository');
    }
}
