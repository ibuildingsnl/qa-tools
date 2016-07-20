<?php

namespace Ibuildings\QaTools\Tool\PhpMd\Configurator;

use Ibuildings\QaTools\Core\Configuration\ConfigurationBuilder;
use Ibuildings\QaTools\Core\Configurator\Configurator;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Tool\PhpMd\PhpMd;

final class PhpMdSf2Configurator implements Configurator
{
    public function configure(ConfigurationBuilder $configurationBuilder, Interviewer $interviewer)
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
