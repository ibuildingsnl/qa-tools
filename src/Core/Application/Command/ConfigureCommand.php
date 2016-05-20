<?php

namespace Ibuildings\QaTools\Core\Application\Command;

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
            ->setDescription('Configure the Ibuildings QA-tools')
            ->setHelp('Configure the Ibuildings QA-tools');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $interviewer         = $this->getInterviewerFactory()->createWith($input, $output);
        $projectConfigurator = $this->getProjectConfigurator();

        $projectConfigurator->configure($interviewer);
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
}
