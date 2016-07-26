<?php

namespace Ibuildings\QaTools\UnitTest\Core\Configuration;

use Ibuildings\QaTools\Core\Configuration\Configuration;
use Ibuildings\QaTools\Core\Configuration\ConfigurationRepository;
use Ibuildings\QaTools\Core\Interviewer\Answer\Answer;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
use Ibuildings\QaTools\Core\IO\File\FileHandler;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Project\ProjectType;
use Mockery;
use Mockery\Matcher\MatcherAbstract;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase as TestCase;
use PHPUnit_Framework_Assert as Assert;

/**
 * @group Configuration
 */
class ConfigurationRepositoryTest extends TestCase
{
    const FILE_PATH = '/path/to/qa-tools.json';

    /** @test */
    public function can_determine_configuration_exists()
    {
        /** @var MockInterface|FileHandler $fileHandler */
        $fileHandler = Mockery::mock(FileHandler::class);
        $fileHandler->shouldReceive('exists')->andReturn(true);

        $repository = new ConfigurationRepository($fileHandler, self::FILE_PATH);
        $this->assertTrue($repository->configurationExists(), 'Loader should have determined configuration exists');
    }

    /** @test */
    public function can_determine_configuration_does_not_exist()
    {
        /** @var MockInterface|FileHandler $fileHandler */
        $fileHandler = Mockery::mock(FileHandler::class);
        $fileHandler->shouldReceive('exists')->andReturn(false);

        $repository = new ConfigurationRepository($fileHandler, self::FILE_PATH);
        $this->assertFalse(
            $repository->configurationExists(),
            'Loader should have determined configuration does not exist'
        );
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

        $repository = new ConfigurationRepository($fileHandler, self::FILE_PATH);
        $configuration = $repository->load();

        $this->assertSame('Boolean Bust', $configuration->getProject()->getName());
        $this->assertCount(1, $configuration->getProject()->getProjectTypes());
        $this->assertTrue($configuration->getProject()->getProjectTypes()[0]->equals(new ProjectType('php.sf2')));
        $this->assertSame('./', $configuration->getProject()->getConfigurationFilesLocation());
        $this->assertSame(true, $configuration->getProject()->isTravisEnabled());

        $this->assertTrue($configuration->hasAnswer('0772fd2dbcfb028612dab5899a7e5ed5'));
        $this->assertInstanceOf(Answer::class, $configuration->getAnswer('0772fd2dbcfb028612dab5899a7e5ed5'));
    }

    /** @test */
    public function can_save_configuration()
    {
        $configuration = Configuration::create();
        $configuration->reconfigureProject(
            new Project('Terran Tubers', './qa-tools', [new ProjectType('php.drupal8')], false)
        );
        $configuration->answer('4ee0c41472083a7765b17033aab88207', new TextualAnswer('Spacious Salons'));

        /** @var MockInterface|FileHandler $fileHandler */
        $fileHandler = Mockery::spy(FileHandler::class);

        $repository = new ConfigurationRepository($fileHandler, self::FILE_PATH);
        $repository->save($configuration);

        $expectedJson = <<<'JSON'
{
    "projectName": "Terran Tubers",
    "configurationFilesLocation": ".\/qa-tools",
    "projectTypes": [
        "php.drupal8"
    ],
    "travisEnabled": false,
    "answers": {
        "4ee0c41472083a7765b17033aab88207": "Spacious Salons"
    }
}
JSON;
        $fileHandler->shouldHaveReceived('writeTo')->with(self::eq($expectedJson), selF::FILE_PATH)->once();
    }

    /**
     * Returns a matcher that uses PHPUnit's assertEquals to compare an actual
     * and an expected value. If they differ, a string diff is output.
     *
     * @param string $expected
     * @return MatcherAbstract
     */
    private static function eq($expected)
    {
        return Mockery::on(
            function ($actual) use ($expected) {
                Assert::assertEquals($expected, $actual);
                return true;
            }
        );
    }
}
