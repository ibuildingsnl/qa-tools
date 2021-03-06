<?php

namespace Ibuildings\QaTools\Core\Application\Command;

use Ibuildings\QaTools\Core\Configuration\ConfigurationDumper;
use Ibuildings\QaTools\Core\Configuration\ConfigurationService;
use Ibuildings\QaTools\Core\IO\Cli\InterviewerFactory;
use Ibuildings\QaTools\Core\Project\Directory;
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
        /** @var InterviewerFactory $interviewerFactory */
        $interviewerFactory = $this->container->get('qa_tools.io.cli.interviewer_factory');
        $interviewer = $interviewerFactory->createWith($input, $output);

        /** @var ConfigurationService $service */
        $service = $this->container->get('qa_tools.configuration_service');
        if (!$service->configureProject($interviewer, new Directory(getcwd()))) {
            return 1;
        }
    }
}
