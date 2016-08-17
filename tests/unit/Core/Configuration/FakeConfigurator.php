<?php

namespace Ibuildings\QaTools\UnitTest\Core\Configuration;

use Ibuildings\QaTools\Core\Configuration\RequirementDirectory;
use Ibuildings\QaTools\Core\Configuration\RequirementHelperSet;
use Ibuildings\QaTools\Core\Configurator\Configurator;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;

final class FakeConfigurator implements Configurator
{
    private $toolClassName;

    public function __construct($toolClassName)
    {
        $this->toolClassName = $toolClassName;
    }

    public function configure(Interviewer $interviewer, RequirementDirectory $requirementDirectory, RequirementHelperSet $requirementHelperSet)
    {
    }

    public function getToolClassName()
    {
        return $this->toolClassName;
    }
}
