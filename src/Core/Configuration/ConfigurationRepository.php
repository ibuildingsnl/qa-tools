<?php

namespace Ibuildings\QaTools\Core\Configuration;

interface ConfigurationRepository
{
    /**
     * @return bool
     */
    public function configurationExists();

    /**
     * @return Configuration
     */
    public function load();

    /**
     * @param Configuration $configuration
     * @return void
     */
    public function save(Configuration $configuration);
}
