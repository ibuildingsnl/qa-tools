<?php

namespace Ibuildings\QaTools\Tool\Behat\Initialiser;

use Ibuildings\QaTools\Core\Project\Directory;
use Symfony\Component\Process\ProcessBuilder;

class BehatInitialiser
{
    public function initialise(Directory $projectRoot)
    {
        $process = ProcessBuilder::create(['vendor/bin/behat', '--init'])
            ->setWorkingDirectory($projectRoot->getDirectory())
            ->getProcess();

        $process->run();
    }
}
