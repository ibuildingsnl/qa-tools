<?php

namespace Ibuildings\QaTools\Core\Application\Command;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Configuration\Configuration;
use Ibuildings\QaTools\Core\Configuration\ConfigurationBuilderFactory;
use Ibuildings\QaTools\Core\Configuration\ConfigurationDumper;
use Ibuildings\QaTools\Core\Configuration\ConfigurationLoader;
use Ibuildings\QaTools\Core\Configurator\Configurator;
use Ibuildings\QaTools\Core\Configurator\RunList;
use Ibuildings\QaTools\Core\IO\Cli\InterviewerFactory;
use Ibuildings\QaTools\Core\Project\ProjectConfigurator;
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
        $configurationLoader = $this->getConfigurationLoader();

        $previousAnswers = [];
        if ($configurationLoader->configurationExists()) {
            $configuration = $configurationLoader->load();
            $previousAnswers  = $configuration->getAnswers();
        }

        $interviewer         = $this->getInterviewerFactory()->createMemorizingWith($input, $output, $previousAnswers);
        $projectConfigurator = $this->getProjectConfigurator();
        $runList             = $this->getRunList();

        $project = $projectConfigurator->configure($interviewer);

        $configurationBuilder = $this->getConfigurationBuilderFactory()->createWithProject($project);

        /** @var Configurator $configurator */
        foreach ($runList->getConfiguratorsForProjectTypes($project->getProjectTypes()) as $configurator) {
            $interviewer->setScope($configurator->getToolClassName());

            $templatePath = $this->getTemplatePathForTool($configurator->getToolClassName());
            $configurationBuilder->setTemplatePath($templatePath);

            $configurator->configure($configurationBuilder, $interviewer);
        }

        $configuration = new Configuration($project, $interviewer->getGivenAnswers());
        $this->getConfigurationDumper()->dump($configuration);
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
     * @return RunList
     */
    private function getRunList()
    {
        return $this->container->get('qa_tools.run_list');
    }

    /**
     * @return ConfigurationBuilderFactory
     */
    protected function getConfigurationBuilderFactory()
    {
        return $this->container->get('qa_tools.configuration.configuration_builder.factory');
    }

    /**
     * @param string $toolClassName
     * @return string
     */
    private function getTemplatePathForTool($toolClassName)
    {
        Assertion::string($toolClassName);

        return $this->container->getParameter('tool.' . $toolClassName . '.resource_path') . '/templates';
    }

    /**
     * @return ConfigurationLoader
     */
    private function getConfigurationLoader()
    {
        return $this->container->get('qa_tools.configuration.configuration_loader');
    }

    /**
     * @return ConfigurationDumper
     */
    private function getConfigurationDumper()
    {
        return $this->container->get('qa_tools.configuration.configuration_dumper');
    }
}
