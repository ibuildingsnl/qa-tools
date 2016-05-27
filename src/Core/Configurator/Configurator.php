<?php

namespace Ibuildings\QaTools\Core\Configurator;

use Ibuildings\QaTools\Core\Configuration\ConfigurationBuilder;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;

interface Configurator
{
    /**
     * @param ConfigurationBuilder $configurationBuilder
     * @param Interviewer $interviewer
     * @return void
     */
    public function configure(ConfigurationBuilder $configurationBuilder, Interviewer $interviewer);

    /**
     * @return string
     */
    public function getToolClassName();
}

