<?php

namespace Ibuildings\QaTools\UnitTest\Core\Task\Executor;

use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\AutomatedResponseInterviewer;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Npm\CliNpmProject;
use Ibuildings\QaTools\Core\Npm\CliNpmProjectFactory;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Task\Executor\InstallNpmDevDependencyTaskExecutor;
use Ibuildings\QaTools\Core\Task\InstallNpmDevDependencyTask;
use Ibuildings\QaTools\Core\Task\Task;
use Ibuildings\QaTools\Core\Task\TaskList;
use Mockery;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;

/**
 * @group Task
 * @group TaskExecutor
 */
class InstallNpmDevDependencyTaskExecutorTest extends TestCase
{
    /** @var Project|Mock */
    private $project;
    /** @var Interviewer|Mock */
    private $interviewer;
    /** @var InstallNpmDevDependencyTaskExecutor */
    private $executor;
    /** @var CliNpmProject|Mock */
    private $npmProject;
    /** @var CliNpmProjectFactory|Mock */
    private $npmProjectFactory;

    protected function setUp()
    {
        $this->npmProject = Mockery::spy(CliNpmProject::class);
        $this->npmProjectFactory = Mockery::mock(CliNpmProjectFactory::class);
        $this->npmProjectFactory->shouldReceive('forDirectory')->with('./')->andReturn($this->npmProject);

        $this->project = Mockery::mock(Project::class);
        $this->interviewer = Mockery::mock(Interviewer::class);
        $this->interviewer->shouldReceive('notice');

        $this->executor = new InstallNpmDevDependencyTaskExecutor($this->npmProjectFactory);
    }

    /**
     * @test
     */
    public function supports_install_npm_dev_dependency_tasks()
    {
        $task = new InstallNpmDevDependencyTask('foobar', '1.0');
        $this->assertTrue(
            $this->executor->supports($task),
            'InstallNpmDevDependencyTaskExecutor should be able to handle InstallNpmDevDependencyTask instance'
        );
    }

    /**
     * @test
     */
    public function does_not_support_any_other_kind_of_task()
    {
        /** @var Task|Mock $task */
        $task = Mockery::mock(Task::class);
        $this->assertFalse(
            $this->executor->supports($task),
            'InstallNpmDevDependencyTaskExecutor should only support InstallNpmDevDependencyTask instances'
        );
    }

    /**
     * @test
     */
    public function prerequisites_are_met_when_package_could_be_installed()
    {
        $this->npmProject->shouldReceive('isInitialised')->andReturn(true);
        $this->project->shouldReceive('getRootDirectory->getDirectory')->andReturn('./');

        $this->npmProject
            ->shouldReceive('verifyDevDependenciesCanBeInstalled')
            ->with(['eslint@3.10.0'])
            ->andReturn(true);

        $taskList = new TaskList([new InstallNpmDevDependencyTask('eslint', '3.10.0')]);
        $this->assertTrue($this->executor->arePrerequisitesMet($taskList, $this->project, $this->interviewer));
    }

    /**
     * @test
     */
    public function prerequisites_are_not_met_when_package_could_not_be_installed()
    {
        $this->npmProject->shouldReceive('isInitialised')->andReturn(true);
        $this->project->shouldReceive('getRootDirectory->getDirectory')->andReturn('./');

        $this->npmProject
            ->shouldReceive('verifyDevDependenciesCanBeInstalled')
            ->with(['brokenpackage@1.0.0'])
            ->andReturn(false);

        $taskList = new TaskList([new InstallNpmDevDependencyTask('brokenpackage', '1.0.0')]);
        $this->assertFalse($this->executor->arePrerequisitesMet($taskList, $this->project, $this->interviewer));
    }

    /**
     * @test
     */
    public function npm_project_is_initialised_if_not_initialised_and_user_wants_this()
    {
        $this->npmProject->shouldReceive('isInitialised')->andReturn(false);

        $this->project->shouldReceive('getRootDirectory->getDirectory')->andReturn('./');
        $taskList = new TaskList([new InstallNpmDevDependencyTask('eslint', '3.10.0')]);

        $interviewer = new AutomatedResponseInterviewer();
        $interviewer->recordAnswer('Initialise one?', YesOrNoAnswer::yes());

        $this->npmProject->shouldReceive('verifyDevDependenciesCanBeInstalled')->andReturn(true);

        $this->executor->arePrerequisitesMet($taskList, $this->project, $interviewer);

        $this->npmProject->shouldHaveReceived('initialise');
    }

    /**
     * @test
     */
    public function npm_project_is_not_initialised_if_user_does_not_want_this()
    {
        $this->npmProject->shouldReceive('isInitialised')->andReturn(false);

        $this->project->shouldReceive('getRootDirectory->getDirectory')->andReturn('./');
        $taskList = new TaskList([new InstallNpmDevDependencyTask('eslint', '3.10.0')]);

        $interviewer = new AutomatedResponseInterviewer();
        $interviewer->recordAnswer('Initialise one?', YesOrNoAnswer::no());

        $this->executor->arePrerequisitesMet($taskList, $this->project, $interviewer);

        $this->npmProject->shouldNotHaveReceived('initialise');
    }
}
