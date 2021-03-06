<?php
namespace Ibuildings\QaTools\UnitTest\Core\Task\Executor;

use Hamcrest\Matchers as Match;
use Ibuildings\QaTools\Core\Build\Build;
use Ibuildings\QaTools\Core\Build\Snippet;
use Ibuildings\QaTools\Core\Build\Tool;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\IO\File\FileHandler;
use Ibuildings\QaTools\Core\Project\Directory;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Task\AddAntBuildTask;
use Ibuildings\QaTools\Core\Task\Executor\AddBuildTaskExecutor;
use Ibuildings\QaTools\Core\Task\Task;
use Ibuildings\QaTools\Core\Task\TaskList;
use Ibuildings\QaTools\Core\Templating\TemplateEngine;
use Ibuildings\QaTools\Test\MockeryTestCase;
use Mockery;
use Mockery\MockInterface;

/**
 * @group Task
 * @group TaskExecutor
 */
class AddBuildTaskExecutorTest extends MockeryTestCase
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
        $this->executor = new AddBuildTaskExecutor(
            $this->fileHandler,
            $this->templateEngine,
            '',
            ['phplint', 'phpmd', 'phpcs']
        );
        $this->project = Mockery::mock(Project::class);
        $this->interviewer = Mockery::spy(Interviewer::class);
    }

    /** @test */
    public function supports_execution_of_add_build_tasks()
    {
        $this->assertTrue(
            $this->executor->supports(new AddAntBuildTask(Build::preCommit(), Tool::withIdentifier('phpmd'), Snippet::withContentsAndTargetName('data', 'target'))),
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
        $tasks = new TaskList([new AddAntBuildTask(Build::preCommit(), Tool::withIdentifier('phpmd'), Snippet::withContentsAndTargetName('data', 'target'))]);
        $this->project->shouldReceive('getConfigurationFilesLocation')->andReturn(new Directory('.'));
        $this->fileHandler
            ->shouldReceive('canWriteWithBackupTo')
            ->with('./build.xml')
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
        $tasks = new TaskList([new AddAntBuildTask(Build::preCommit(), Tool::withIdentifier('phpmd'), Snippet::withContentsAndTargetName('data', 'target'))]);
        $this->project->shouldReceive('getConfigurationFilesLocation')->andReturn(new Directory('.'));
        $this->fileHandler
            ->shouldReceive('canWriteWithBackupTo')
            ->with('./build.xml')
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
        $tasks = new TaskList([new AddAntBuildTask(Build::preCommit(), Tool::withIdentifier('phpmd'), Snippet::withContentsAndTargetName('data', 'target'))]);
        $this->project->shouldReceive('getConfigurationFilesLocation')->andReturn(new Directory('.'));
        $this->templateEngine->shouldReceive('render')->andReturn('data')->once();
        $this->executor->execute($tasks, $this->project, $this->interviewer);
        $this->fileHandler
            ->shouldHaveReceived('writeWithBackupTo')
            ->with('./build.xml', Match::containsString('data'))
            ->once();
    }

    /** @test */
    public function adds_snippets_in_prioritized_order()
    {
        $tasks = new TaskList([
            new AddAntBuildTask(Build::preCommit(), Tool::withIdentifier('phpmd'), Snippet::withContentsAndTargetName('data', 'phpmdSnippet')),
            new AddAntBuildTask(Build::preCommit(), Tool::withIdentifier('phplint'), Snippet::withContentsAndTargetName('data', 'phplintSnippet')),
            new AddAntBuildTask(Build::preCommit(), Tool::withIdentifier('phpcs'), Snippet::withContentsAndTargetName('data', 'phpcsSnippet')),
        ]);

        $this->project->shouldReceive('getConfigurationFilesLocation')->andReturn(new Directory('.'));
        $this->templateEngine->shouldReceive('render')->andReturn('data')->once();
        $this->executor->execute($tasks, $this->project, $this->interviewer);

        $this->templateEngine
            ->shouldHaveReceived('render')
            ->with('build.xml.twig', Match::arrayContaining(
                [
                    'main_snippets' => [],
                    'main_targets' => [],
                    'precommit_snippets' => ['data', 'data', 'data'],
                    'precommit_targets' => ['phplintSnippet', 'phpmdSnippet', 'phpcsSnippet']
                ]
            ))
            ->once();
    }

    /** @test */
    public function rolls_back_changes_to_files()
    {
        $tasks = new TaskList(
            [
                new AddAntBuildTask(Build::preCommit(), Tool::withIdentifier('phpcs'), Snippet::withContentsAndTargetName('data', 'target')),
                new AddAntBuildTask(Build::preCommit(), Tool::withIdentifier('phpmd'), Snippet::withContentsAndTargetName('data', 'target'))
            ]
        );
        $this->project->shouldReceive('getConfigurationFilesLocation')->andReturn(new Directory('.'));
        $this->executor->execute($tasks, $this->project, $this->interviewer);
        $this->executor->rollBack($tasks, $this->project, $this->interviewer);

        $this->fileHandler
            ->shouldHaveReceived('restoreBackupOf')
            ->with('./build.xml')
            ->once();
    }

    /** @test */
    public function sorting_tasks()
    {
        $tool = Tool::withIdentifier('phpmd');
        $this->assertEquals(1, $tool->compare(Tool::withIdentifier('phpcs'), ['phpcs', 'phpmd', 'phplint']));
        $this->assertEquals(0, $tool->compare(Tool::withIdentifier('phpmd'), ['phpcs', 'phpmd', 'phplint']));
        $this->assertEquals(-1, $tool->compare(Tool::withIdentifier('phplint'), ['phpcs', 'phpmd', 'phplint']));
    }
}
