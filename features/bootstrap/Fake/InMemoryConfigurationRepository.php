<?php

namespace Fake;

use Ibuildings\QaTools\Core\Configuration\Configuration;
use Ibuildings\QaTools\Core\Configuration\ConfigurationRepository;
use RuntimeException;

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
            throw new RuntimeException('No configuration stored');
        }

        return $this->storedConfiguration;
    }

    public function save(Configuration $configuration)
    {
        $this->storedConfiguration = $configuration;
    }
}
