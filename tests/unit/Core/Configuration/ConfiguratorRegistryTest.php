<?php

namespace Ibuildings\QaTools\UnitTest\Core\Configuration;

use Ibuildings\QaTools\Core\Configurator\ConfiguratorRegistry;
use Ibuildings\QaTools\Core\Exception\LogicException;
use Ibuildings\QaTools\Core\Project\ProjectType;
use Mockery as m;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * @group Configuration
 */
class ConfiguratorRegistryTest extends TestCase
{
    /** @test */
    public function a_configurator_can_be_registered()
    {
        $registry = new ConfiguratorRegistry();
        $registry->registerFor(new FakeConfigurator(FooTool::class), new ProjectType('php.sf2'));
    }

    /** @test */
    public function two_configurators_for_two_different_tools_can_be_registered_under_the_same_project_type()
    {
        $registry = new ConfiguratorRegistry();
        $registry->registerFor(new FakeConfigurator(FooTool::class), new ProjectType('php.drupal8'));
        $registry->registerFor(new FakeConfigurator(BarTool::class), new ProjectType('php.drupal8'));
    }

    /** @test */
    public function the_same_configurator_can_be_registered_under_two_different_project_types()
    {
        $registry = new ConfiguratorRegistry();

        $configurator = new FakeConfigurator(FooTool::class);
        $registry->registerFor($configurator, new ProjectType('php.other'));
        $registry->registerFor($configurator, new ProjectType('php.drupal7'));
    }

    /** @test */
    public function two_configurators_for_the_same_tool_can_not_be_registered_under_the_same_project_type()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('same tool');

        $registry = new ConfiguratorRegistry();
        $registry->registerFor(new FakeConfigurator(FooTool::class), new ProjectType('js.angular1'));
        $registry->registerFor(new FakeConfigurator(FooTool::class), new ProjectType('js.angular1'));
    }
}
