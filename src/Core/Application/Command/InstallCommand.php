<?php

namespace Ibuildings\QaTools\Core\Application\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class InstallCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('install')
            ->setDescription('Setup for the Ibuildings QA-tools')
            ->setHelp('Setup for the Ibuildings QA-tools');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Implementation
    }
}
