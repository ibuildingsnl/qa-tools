<?php

namespace Ibuildings\QaTools\UnitTest\Core\Configuration;

use Ibuildings\QaTools\Core\Configuration\ConfigurationLoader;
use Ibuildings\QaTools\Core\IO\File\FileHandler;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * @group Configuration
 */
class ConfigurationLoaderTest extends TestCase
{
    const FILE_PATH = '/path/to/qa-tools.json';

    /** @test */
    public function can_determine_configuration_exists()
    {
        /** @var MockInterface|FileHandler $fileHandler */
        $fileHandler = Mockery::mock(FileHandler::class);
        $fileHandler->shouldReceive('exists')->andReturn(true);

        $loader = new ConfigurationLoader($fileHandler, self::FILE_PATH);
        $this->assertTrue($loader->configurationExists(), 'Loader should have determined configuration exists');
    }

    /** @test */
    public function can_determine_configuration_does_not_exist()
    {
        /** @var MockInterface|FileHandler $fileHandler */
        $fileHandler = Mockery::mock(FileHandler::class);
        $fileHandler->shouldReceive('exists')->andReturn(false);

        $loader = new ConfigurationLoader($fileHandler, self::FILE_PATH);
        $this->assertFalse($loader->configurationExists(), 'Loader should have determined configuration does not exist');
    }

    /** @test */
    public function can_load_configuration()
    {
        $json = <<<'JSON'
{
    "projectName": "Boolean Bust",
    "configurationFilesLocation": ".\/",
    "projectTypes": [
        "php.sf2"
    ],
    "travisEnabled": true,
    "answers": {
        "13289614b49c23d12ae6936a5156afb7": "Boolean Bust",
        "0772fd2dbcfb028612dab5899a7e5ed5": ".\/",
        "21543c141402b88dc03108b318e49b83": "PHP",
        "5e076564011e4ddb583f495b8e5f82c7": "Symfony 2",
        "4ee0c41472083a7765b17033aab88207": true
    }
}
JSON;


        /** @var MockInterface|FileHandler $fileHandler */
        $fileHandler = Mockery::mock(FileHandler::class);
        $fileHandler->shouldReceive('exists')->andReturn(true);
        $fileHandler->shouldReceive('readFrom')->with(self::FILE_PATH)->andReturn($json);

        $loader = new ConfigurationLoader($fileHandler, self::FILE_PATH);
        $loader->load();
    }
}
