<?php

namespace Ibuildings\QaTools\Core\Configurator;

use Ibuildings\QaTools\Core\ConfigurationBuilder\ConfigurationBuilder;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;

interface Configurator
{
    public function configure(ConfigurationBuilder $configurationBuilder, Interviewer $interviewer);
}

