<?php

namespace Ibuildings\QaTools\UnitTest\Core\Configuration;

use Ibuildings\QaTools\Core\Configuration\ConfigurationBuilder;
use Ibuildings\QaTools\Core\Exception\InvalidArgumentException;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Task\Runner\TaskList;
use Ibuildings\QaTools\Core\Templating\TemplateEngine;
use Ibuildings\QaTools\UnitTest\Core\Task\FakeTask;
use Mockery;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @group Configuration
 * @group Task
 */
class ConfigurationBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function configuration_builder_initializes_with_an_empty_tasklist()
    {
        $dummyTemplateEngine = Mockery::mock(TemplateEngine::class);
        $dummyProject        = Mockery::mock(Project::class);

        $configurationBuilder = new ConfigurationBuilder($dummyTemplateEngine, $dummyProject);

        $expectedTaskList = new TaskList([]);
        $actualTaskList   = $configurationBuilder->getTaskList();

        $this->assertEquals($expectedTaskList, $actualTaskList);
    }

    /**
     * @test
     */
    public function the_project_given_during_instantiation_can_be_retrieved_from_the_configuration_builder()
    {
        $dummyTemplateEngine = Mockery::mock(TemplateEngine::class);
        $dummyProject        = Mockery::mock(Project::class);

        $configurationBuilder = new ConfigurationBuilder($dummyTemplateEngine, $dummyProject);

        $retrievedProject = $configurationBuilder->getProject();

        $this->assertEquals($dummyProject, $retrievedProject);
    }

    /**
     * @test
     */
    public function a_task_can_be_added_to_the_configuration_builders_task_list()
    {
        $dummyTemplateEngine = Mockery::mock(TemplateEngine::class);
        $dummyProject        = Mockery::mock(Project::class);

        $fakeTask = new FakeTask('Some task');
        $expectedTaskList = new TaskList([$fakeTask]);

        $configurationBuilder = new ConfigurationBuilder($dummyTemplateEngine, $dummyProject);
        $configurationBuilder->addTask($fakeTask);

        $actualTaskList = $configurationBuilder->getTaskList();

        $this->assertEquals($expectedTaskList, $actualTaskList);
    }

    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notStringOrEmptyString
     */
    public function a_template_path_that_can_be_set_through_the_configuration_builder_can_only_be_a_string($path)
    {
        $this->expectException(InvalidArgumentException::class);

        $dummyTemplateEngine = Mockery::mock(TemplateEngine::class);
        $dummyProject        = Mockery::mock(Project::class);

        $configurationBuilder = new ConfigurationBuilder($dummyTemplateEngine, $dummyProject);
        $configurationBuilder->setTemplatePath($path);
    }

    /**
     * @test
     */
    public function a_template_path_is_set_through_the_configuration_builder()
    {
        $dummyTemplateEngine = Mockery::mock(TemplateEngine::class);
        $dummyProject        = Mockery::mock(Project::class);

        $path = 'some/file/path';

        $dummyTemplateEngine
            ->shouldReceive('setPath')
            ->with($path);

        $configurationBuilder = new ConfigurationBuilder($dummyTemplateEngine, $dummyProject);
        $configurationBuilder->setTemplatePath($path);
    }

    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notStringOrEmptyString
     */
    public function a_template_that_can_be_rendered_through_the_configuration_builder_can_only_be_a_string($template)
    {
        $this->expectException(InvalidArgumentException::class);

        $dummyTemplateEngine = Mockery::mock(TemplateEngine::class);
        $dummyProject        = Mockery::mock(Project::class);

        $configurationBuilder = new ConfigurationBuilder($dummyTemplateEngine, $dummyProject);
        $configurationBuilder->renderTemplate($template);
    }

    /**
     * @test
     */
    public function a_template_is_rendered_through_the_configuration_builder()
    {
        $dummyTemplateEngine = Mockery::mock(TemplateEngine::class);
        $dummyProject        = Mockery::mock(Project::class);

        $template = 'some/file/path';

        $dummyTemplateEngine
            ->shouldReceive('render')
            ->with($template, []);

        $configurationBuilder = new ConfigurationBuilder($dummyTemplateEngine, $dummyProject);
        $configurationBuilder->renderTemplate($template);
    }
}
