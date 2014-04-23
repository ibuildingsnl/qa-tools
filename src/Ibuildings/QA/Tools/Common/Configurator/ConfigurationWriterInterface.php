<?php

namespace Ibuildings\QA\Tools\Common\Configurator;

interface ConfigurationWriterInterface extends ConfiguratorInterface
{
    /**
     * Writes the config to a file
     *
     * @codeCoverageIgnore
     */
    public function writeConfig();

    /**
     * checks if a file should be written to the filesystem or not
     *
     * @return boolean
     */
    public function shouldWrite();
}
