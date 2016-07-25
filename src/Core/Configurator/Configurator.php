<?php

namespace Ibuildings\QaTools\Core\Configurator;

use Ibuildings\QaTools\Core\Configuration\TaskHelperSet;
use Ibuildings\QaTools\Core\Configuration\TaskRegistry;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;

interface Configurator
{
    /**
     * @param Interviewer   $interviewer
     * @param TaskRegistry  $taskRegistry
     * @param TaskHelperSet $taskHelperSet
     * @return void
     */
    public function configure(Interviewer $interviewer, TaskRegistry $taskRegistry, TaskHelperSet $taskHelperSet);

    /**
     * @return string
     */
    public function getToolClassName();
}

