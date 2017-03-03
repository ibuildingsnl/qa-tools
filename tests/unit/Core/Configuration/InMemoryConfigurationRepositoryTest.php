<?php

namespace Ibuildings\QaTools\UnitTest\Core\Configuration;

use Ibuildings\QaTools\Core\Configuration\Configuration;
use Ibuildings\QaTools\Core\Configuration\InMemoryConfigurationRepository;
use Ibuildings\QaTools\Core\Exception\RuntimeException;
use Ibuildings\QaTools\Test\MockeryTestCase;

/**
 * @group Configuration
 */
class InMemoryConfigurationRepositoryTest extends MockeryTestCase
{
    /** @test */
    public function has_no_configuration_in_memory_on_construction()
    {
        $repository = new InMemoryConfigurationRepository();
        $this->assertFalse($repository->configurationExists(), 'Repository should not have configuration in memory');
    }

    /** @test */
    public function stores_configuration()
    {
        $configuration = Configuration::create();

        $repository = new InMemoryConfigurationRepository();
        $repository->save($configuration);

        $this->assertTrue($repository->configurationExists(), 'Repository should have configuration in memory');
        $this->assertSame(
            $configuration,
            $repository->load(),
            'Repository should have the same configuration in memory'
        );
    }

    /** @test */
    public function throws_an_exception_when_attempting_to_load_nonexistent_configuration()
    {
        $repository = new InMemoryConfigurationRepository();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No configuration stored in memory');
        $repository->load();
    }
}
