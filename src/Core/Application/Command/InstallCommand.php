<?php

namespace Ibuildings\QaTools\Core\Application\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class InstallCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected function configure()
    {
        $this
            ->setName('install')
            ->setDescription('Setup for the Ibuildings QA-tools')
            ->setHelp('Setup for the Ibuildings QA-tools');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $questionHandler = $this->container
            ->get('question_handler_factory')
            ->createWith($input, $output);

        $installer = $this->container
            ->get('installer');

        $installer->install($questionHandler);
    }
}
