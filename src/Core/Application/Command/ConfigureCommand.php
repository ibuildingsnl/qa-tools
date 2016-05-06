<?php

namespace Ibuildings\QaTools\Core\Application\Command;

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
        // Implementation
    }
}
