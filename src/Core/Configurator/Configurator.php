<?php

namespace Ibuildings\QaTools\Core\Configurator;

use Ibuildings\QaTools\Core\Configuration\RequirementDirectory;
use Ibuildings\QaTools\Core\Configuration\RequirementHelperSet;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;

interface Configurator
{
    /**
     * @param Interviewer $interviewer
     * @param RequirementDirectory $requirementDirectory
     * @param RequirementHelperSet $requirementHelperSet
     * @return void
     */
    public function configure(
        Interviewer $interviewer,
        RequirementDirectory $requirementDirectory,
        RequirementHelperSet $requirementHelperSet
    );

    /**
     * @return string
     */
    public function getToolClassName();
}
