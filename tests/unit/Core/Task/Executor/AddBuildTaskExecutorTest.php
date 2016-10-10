<?php

namespace Ibuildings\QaTools\UnitTest\Core\Task\Executor;

use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\IO\File\FileHandler;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Stages\Precommit;
use Ibuildings\QaTools\Core\Task\Executor\AddBuildTaskExecutor;
use Ibuildings\QaTools\Core\Task\Task;
use Ibuildings\QaTools\Core\Task\TaskList;
use Ibuildings\QaTools\Core\Task\AddBuildTask;
use Ibuildings\QaTools\Core\Templating\TemplateEngine;
use Mockery;
use Mockery as m;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase as TestCase;
use Hamcrest\Matchers as Match;

/**
 * @group Task
 * @group TaskExecutor
 */
class AddBuildTaskExecutorTest extends TestCase
{
    /** @var TemplateEngine|MockInterface */
    private $templateEngine;
    /** @var FileHandler|MockInterface */
    private $fileHandler;
    /** @var Project|MockInterface */
    private $project;
    /** @var Interviewer|MockInterface */
    private $interviewer;
    /** @var AddBuildTaskExecutor */
    private $executor;

    protected function setUp()
    {
        $this->fileHandler = Mockery::spy(FileHandler::class);
        $this->templateEngine = Mockery::spy(TemplateEngine::class);
        $this->executor = new AddBuildTaskExecutor($this->fileHandler, $this->templateEngine, '');

        $this->project = Mockery::mock(Project::class);
        $this->interviewer = Mockery::spy(Interviewer::class);
    }

    /** @test */
    public function supports_execution_of_add_build_tasks()
    {
        $this->assertTrue(
            $this->executor->supports(new AddBuildTask(new Precommit(), 'data', 'targetName')),
            'AddBuildTaskExecutor should support execution of AddBuildTasks'
        );
    }

    /** @test */
    public function does_not_support_execution_of_tasks_other_than_add_build_tasks()
    {
        $this->assertFalse(
            $this->executor->supports(Mockery::mock(Task::class)),
            'AddBuildTaskExecutor should not support execution of any tasks other than AddBuildTasks'
        );
    }

    /** @test */
    public function checks_that_the_files_can_be_written()
    {
        $tasks = new TaskList([new AddBuildTask(new Precommit(), 'data', 'targetName')]);

        $this->project->shouldReceive('getConfigurationFilesLocation->getDirectory')->andReturn('');

        $this->fileHandler
            ->shouldReceive('canWriteWithBackupTo')
            ->with('/build.xml')
            ->once()
            ->andReturn(true);

        $this->assertTrue(
            $this->executor->arePrerequisitesMet($tasks, $this->project, $this->interviewer),
            'Writing to file should be possible, but, unexpectedly, it is not'
        );

        $this->interviewer->shouldNotHaveReceived('warn');
    }

    /** @test */
    public function throws_an_exception_when_the_files_cannot_be_written()
    {
        $tasks = new TaskList([new AddBuildTask(new Precommit(), 'data', 'targetName')]);

        $this->project->shouldReceive('getConfigurationFilesLocation->getDirectory')->andReturn('');

        $this->fileHandler
            ->shouldReceive('canWriteWithBackupTo')
            ->with('/build.xml')
            ->once()
            ->andReturn(false);

        $this->assertFalse(
            $this->executor->arePrerequisitesMet($tasks, $this->project, $this->interviewer),
            'Writing to file should not be possible, but, unexpectedly, it is'
        );

        $this->interviewer->shouldHaveReceived('warn')->atLeast()->once();
    }

    /** @test */
    public function writes_files_with_backups()
    {
        $tasks = new TaskList([new AddBuildTask(new Precommit(), 'data', 'targetName')]);

        $this->project->shouldReceive('getConfigurationFilesLocation->getDirectory')->andReturn('');

        $this->templateEngine->shouldReceive('render')->andReturn('data')->once();

        $this->executor->execute($tasks, $this->project, $this->interviewer);

        $this->fileHandler
            ->shouldHaveReceived('writeWithBackupTo')
            ->with('build.xml', Match::containsString('data'))
            ->once();
    }

    /** @test */
    public function rolls_back_changes_to_files()
    {
        $tasks = new TaskList(
            [
                new AddBuildTask(new Precommit(), 'data', 'targetName'),
                new AddBuildTask(new Precommit(), 'data', 'targetName')
            ]
        );

        $this->project->shouldReceive('getConfigurationFilesLocation->getDirectory')->andReturn('');

        $this->executor->execute($tasks, $this->project, $this->interviewer);
        $this->executor->rollBack($tasks, $this->project, $this->interviewer);

        $this->fileHandler
            ->shouldHaveReceived('restoreBackupOf')
            ->with('build.xml')
            ->once();
    }
}
