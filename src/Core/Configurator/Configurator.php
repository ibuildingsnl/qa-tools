<?php

namespace Ibuildings\QaTools\Core\Configurator;

use Ibuildings\QaTools\Core\Configuration\TaskDirectory;
use Ibuildings\QaTools\Core\Configuration\TaskHelperSet;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;

interface Configurator
{
    /**
     * @param Interviewer   $interviewer
     * @param TaskDirectory $taskDirectory
     * @param TaskHelperSet $taskHelperSet
     * @return void
     */
    public function configure(Interviewer $interviewer, TaskDirectory $taskDirectory, TaskHelperSet $taskHelperSet);

    /**
     * @return string
     */
    public function getToolClassName();
}
