<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Exception\RuntimeException;

final class InMemoryConfigurationRepository implements ConfigurationRepository
{
    /**
     * @var Configuration|null
     */
    private $storedConfiguration;

    public function configurationExists()
    {
        return $this->storedConfiguration !== null;
    }

    public function load()
    {
        if (!$this->configurationExists()) {
            throw new RuntimeException('No configuration stored in memory');
        }

        return $this->storedConfiguration;
    }

    public function save(Configuration $configuration)
    {
        $this->storedConfiguration = $configuration;
    }
}
