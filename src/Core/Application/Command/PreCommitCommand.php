<?php

namespace Ibuildings\QaTools\Core\Application\Command;

use Ibuildings\QaTools\Core\IO\Cli\InterviewerFactory;
use Ibuildings\QaTools\Core\Project\Directory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class PreCommitCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected function configure()
    {
        $this
            ->setName('configure:pre-commit')
            ->setDescription('Configure the pre-commit hook for the Ibuildings QA Tools')
            ->setHelp('Configure the pre-commit hook for the Ibuildings QA Tools');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var InterviewerFactory $interviewerFactory */
        $interviewerFactory = $this->container->get('qa_tools.io.cli.interviewer_factory');
        $interviewer = $interviewerFactory->createWith($input, $output);

        $installer = $this->container->get('qa_tools.git.hook_installer');
        $projectRoot = new Directory(getcwd());

        $installer->installPreCommitHook($interviewer, $projectRoot);
    }
}
