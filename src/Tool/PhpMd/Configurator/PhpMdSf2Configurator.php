<?php

namespace Ibuildings\QaTools\Tool\PhpMd\Configurator;

use Ibuildings\QaTools\Core\Configuration\TaskHelperSet;
use Ibuildings\QaTools\Core\Configuration\TaskRegistry;
use Ibuildings\QaTools\Core\Configurator\Configurator;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Tool\PhpMd\PhpMd;

final class PhpMdSf2Configurator implements Configurator
{
    public function configure(Interviewer $interviewer, TaskRegistry $taskRegistry, TaskHelperSet $taskHelperSet)
    {
        // Configure tool
    }

    /**
     * @return string
     */
    public function getToolClassName()
    {
        return PhpMd::class;
    }
}
