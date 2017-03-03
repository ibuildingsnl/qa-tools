<?php

namespace Ibuildings\QaTools\UnitTest\Core\Configuration;

use Ibuildings\QaTools\Core\Configurator\ConfiguratorRepository;
use Ibuildings\QaTools\Core\Exception\LogicException;
use Ibuildings\QaTools\Core\Project\ProjectType;
use Ibuildings\QaTools\Test\MockeryTestCase;

/**
 * @group Configuration
 */
class ConfiguratorRepositoryTest extends MockeryTestCase
{
    /** @test */
    public function a_configurator_can_be_registered()
    {
        $registry = new ConfiguratorRepository();
        $registry->add(new FakeConfigurator(FooTool::class), new ProjectType('php.sf2'));
    }

    /** @test */
    public function two_configurators_for_two_different_tools_can_be_registered_under_the_same_project_type()
    {
        $registry = new ConfiguratorRepository();
        $registry->add(new FakeConfigurator(FooTool::class), new ProjectType('php.drupal8'));
        $registry->add(new FakeConfigurator(BarTool::class), new ProjectType('php.drupal8'));
    }

    /** @test */
    public function the_same_configurator_can_be_registered_under_two_different_project_types()
    {
        $registry = new ConfiguratorRepository();

        $configurator = new FakeConfigurator(FooTool::class);
        $registry->add($configurator, new ProjectType('php.other'));
        $registry->add($configurator, new ProjectType('php.drupal7'));
    }

    /** @test */
    public function two_configurators_for_the_same_tool_can_not_be_registered_under_the_same_project_type()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('same tool');

        $registry = new ConfiguratorRepository();
        $registry->add(new FakeConfigurator(FooTool::class), new ProjectType('js.angular1'));
        $registry->add(new FakeConfigurator(FooTool::class), new ProjectType('js.angular1'));
    }
}
