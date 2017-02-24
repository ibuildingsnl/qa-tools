<?php

namespace Ibuildings\QaTools\UnitTest\Core\Task\Executor;

use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\IO\File\FileHandler;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Task\Executor\WriteFileTaskExecutor;
use Ibuildings\QaTools\Core\Task\Task;
use Ibuildings\QaTools\Core\Task\TaskList;
use Ibuildings\QaTools\Core\Task\WriteFileTask;
use Ibuildings\QaTools\Test\MockeryTestCase;
use Mockery;
use Mockery\MockInterface;

/**
 * @group Task
 * @group TaskExecutor
 */
class WriteFileTaskExecutorTest extends MockeryTestCase
{
    /** @var FileHandler|MockInterface */
    private $fileHandler;
    /** @var Project|MockInterface */
    private $project;
    /** @var Interviewer|MockInterface */
    private $interviewer;
    /** @var WriteFileTaskExecutor */
    private $executor;

    protected function setUp()
    {
        $this->fileHandler = Mockery::spy(FileHandler::class);
        $this->executor = new WriteFileTaskExecutor($this->fileHandler);

        $this->project = Mockery::mock(Project::class);
        $this->interviewer = Mockery::spy(Interviewer::class);
    }

    /** @test */
    public function supports_execution_of_write_file_tasks()
    {
        $this->assertTrue(
            $this->executor->supports(new WriteFileTask('/path/to/file', 'data')),
            'WriteFileTaskExecutor should support execution of WriteFileTasks'
        );
    }

    /** @test */
    public function does_not_support_execution_of_tasks_other_than_write_file_tasks()
    {
        $this->assertFalse(
            $this->executor->supports(Mockery::mock(Task::class)),
            'WriteFileTaskExecutor should not support execution of any tasks other than WriteFileTasks'
        );
    }

    /** @test */
    public function checks_that_the_files_can_be_written()
    {
        $tasks = new TaskList([new WriteFileTask('/path/to/file', 'data')]);

        $this->fileHandler
            ->shouldReceive('canWriteWithBackupTo')
            ->with('/path/to/file')
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
        $tasks = new TaskList([new WriteFileTask('/path/to/file', 'data')]);

        $this->fileHandler
            ->shouldReceive('canWriteWithBackupTo')
            ->with('/path/to/file')
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
        $tasks = new TaskList([new WriteFileTask('/path/to/file', 'data')]);
        $this->executor->execute($tasks, $this->project, $this->interviewer);

        $this->fileHandler
            ->shouldHaveReceived('writeWithBackupTo')
            ->with('/path/to/file', 'data')
            ->once();
    }

    /** @test */
    public function writes_files_with_default_mode()
    {
        $tasks = new TaskList([new WriteFileTask('/path/to/file', 'data')]);
        $this->executor->execute($tasks, $this->project, $this->interviewer);

        $this->fileHandler
            ->shouldHaveReceived('changeMode')
            ->with('/path/to/file', 0644)
            ->once();
    }

    /** @test */
    public function cleans_up_backups()
    {
        $tasks = new TaskList([new WriteFileTask('./file1', 'data'), new WriteFileTask('./file2', 'data')]);
        $this->executor->execute($tasks, $this->project, $this->interviewer);
        $this->executor->cleanUp($tasks, $this->project, $this->interviewer);

        $this->fileHandler
            ->shouldHaveReceived('discardBackupOf')
            ->with('./file1')
            ->once();
        $this->fileHandler
            ->shouldHaveReceived('discardBackupOf')
            ->with('./file2')
            ->once();
    }

    /** @test */
    public function rolls_back_changes_to_files()
    {
        $tasks = new TaskList([new WriteFileTask('./file1', 'data'), new WriteFileTask('./file2', 'data')]);
        $this->executor->execute($tasks, $this->project, $this->interviewer);
        $this->executor->rollBack($tasks, $this->project, $this->interviewer);

        $this->fileHandler
            ->shouldHaveReceived('restoreBackupOf')
            ->with('./file1')
            ->once();
        $this->fileHandler
            ->shouldHaveReceived('restoreBackupOf')
            ->with('./file2')
            ->once();
    }
}
