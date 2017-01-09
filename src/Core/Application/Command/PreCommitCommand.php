<?php

namespace Ibuildings\QaTools\Core\Application\Command;

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
        $installer = $this->container->get('qa_tools.git.hook_installer');

        $installer->installPreCommitHook(new Directory(getcwd()));

        $output->writeln('Installed Git pre-commit hook');
    }
}
