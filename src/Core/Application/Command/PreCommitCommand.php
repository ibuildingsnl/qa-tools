<?php

namespace Ibuildings\QaTools\Core\Application\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class PreCommitCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('install:pre-commit')
            ->setDescription('Sets up the pre-commit hook for the Ibuildings QA-tools')
            ->setHelp('Sets up the pre-commit hook for the Ibuildings QA-tools');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Implementation
    }
}
