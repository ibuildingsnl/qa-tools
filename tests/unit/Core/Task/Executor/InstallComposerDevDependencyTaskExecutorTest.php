<?php

namespace Ibuildings\QaTools\UnitTest\Core\Task\Executor;

use Ibuildings\QaTools\Core\Composer\Package;
use Ibuildings\QaTools\Core\Composer\PackageSet;
use Ibuildings\QaTools\Core\Composer\Project as ComposerProject;
use Ibuildings\QaTools\Core\Composer\ProjectFactory as ComposerProjectFactory;
use Ibuildings\QaTools\Core\Exception\RuntimeException;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Interviewer\Question\YesOrNoQuestion;
use Ibuildings\QaTools\Core\Project\Directory;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Task\Executor\InstallComposerDevDependencyTaskExecutor;
use Ibuildings\QaTools\Core\Task\InstallComposerDevDependencyTask;
use Ibuildings\QaTools\Core\Task\Task;
use Ibuildings\QaTools\Core\Task\TaskList;
use Ibuildings\QaTools\UnitTest\ValueObject;
use Mockery;
use Mockery as m;
use Mockery\Matcher\MatcherAbstract;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * @group Task
 * @group TaskExecutor
 */
class InstallComposerDevDependencyTaskExecutorTest extends TestCase
{
    /** @var ComposerProjectFactory|MockInterface */
    private $composerProjectFactory;
    /** @var ComposerProject|MockInterface */
    private $composerProject;
    /** @var Project|MockInterface */
    private $project;
    /** @var Interviewer|MockInterface */
    private $interviewer;
    /** @var InstallComposerDevDependencyTaskExecutor */
    private $executor;

    protected function setUp()
    {
        $this->composerProject = Mockery::spy(ComposerProject::class);
        $this->composerProjectFactory = Mockery::mock(ComposerProjectFactory::class);
        $this->composerProjectFactory->shouldReceive('forDirectory')->with('./')->andReturn($this->composerProject);

        $this->project = Mockery::mock(Project::class);
        $this->project->shouldReceive('getRootDirectory')->andReturn(new Directory('.'));
        $this->interviewer = Mockery::mock(Interviewer::class);
        $this->interviewer->shouldReceive('notice');
        $this->interviewer->shouldReceive('giveDetails');
        $this->interviewer->shouldReceive('warn');
        $this->interviewer->shouldReceive('success');

        $this->executor = new InstallComposerDevDependencyTaskExecutor($this->composerProjectFactory);
    }

    /** @test */
    public function supports_composer_dev_dependency_tasks()
    {
        $this->assertTrue(
            $this->executor->supports(new InstallComposerDevDependencyTask('menial/monkey', '39')),
            'InstallComposerDevDependencyTaskExecutor should support execution of InstallComposerDevDependencyTasks'
        );
    }

    /** @test */
    public function doesnt_support_other_type_of_tasks()
    {
        $this->assertFalse(
            $this->executor->supports(Mockery::mock(Task::class)),
            'InstallComposerDevDependencyTaskExecutor should not support execution of any tasks other than ' .
            'InstallComposerDevDependencyTasks'
        );
    }

    /** @test */
    public function verifies_that_added_dependencies_dont_conflict_with_current_configuration()
    {
        $this->composerProject->shouldReceive('isInitialised')->andReturn(true);

        $tasks = new TaskList([new InstallComposerDevDependencyTask('lefty/loosy', '2')]);

        $this->assertTrue(
            $this->executor->arePrerequisitesMet($tasks, $this->project, $this->interviewer),
            'Nothing should prevent installation of lefty/loosy 2'
        );

        $expectedPackages = new PackageSet([Package::of('lefty/loosy', '2')]);
        $this->composerProject
            ->shouldHaveReceived('verifyDevDependenciesWillNotConflict')
            ->with(ValueObject::equals($expectedPackages))
            ->once();
    }

    /** @test */
    public function lets_exceptions_from_composer_bubble_up_after_informing_the_user()
    {
        $this->composerProject->shouldReceive('isInitialised')->andReturn(true);

        $tasks = new TaskList([new InstallComposerDevDependencyTask('lefty/loosy', '2')]);

        $expectedPackages = new PackageSet([Package::of('lefty/loosy', '2')]);
        $thrownException = new RuntimeException('A conflict appears');
        $this->composerProject
            ->shouldReceive('verifyDevDependenciesWillNotConflict')
            ->with(ValueObject::equals($expectedPackages))
            ->once()
            ->andThrow($thrownException);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('A conflict appears');
        $this->executor->arePrerequisitesMet($tasks, $this->project, $this->interviewer);

        $this->interviewer->shouldHaveReceived('warn')->atLeast()->once();
    }

    /** @test */
    public function requires_the_dependencies()
    {
        $this->composerProject->shouldReceive('isInitialised')->andReturn(true);

        $tasks = new TaskList([new InstallComposerDevDependencyTask('rambunctious/rake', '3')]);

        $this->executor->arePrerequisitesMet($tasks, $this->project, $this->interviewer);
        $this->executor->execute($tasks, $this->project, $this->interviewer);

        $expectedPackages = new PackageSet([Package::of('rambunctious/rake', '3')]);
        $this->composerProject
            ->shouldHaveReceived('backUpConfiguration')
            ->with()
            ->once();
        $this->composerProject
            ->shouldHaveReceived('requireDevDependencies')
            ->with(ValueObject::equals($expectedPackages))
            ->once();
    }

    /** @test */
    public function cleans_up()
    {
        $this->composerProject->shouldReceive('isInitialised')->andReturn(true);

        $tasks = new TaskList([new InstallComposerDevDependencyTask('rambunctious/rake', '3')]);

        $this->executor->arePrerequisitesMet($tasks, $this->project, $this->interviewer);
        $this->executor->execute($tasks, $this->project, $this->interviewer);
        $this->executor->cleanUp($tasks, $this->project, $this->interviewer);
    }

    /** @test */
    public function rolls_back_the_required_dependencies()
    {
        $this->composerProject->shouldReceive('isInitialised')->andReturn(true);

        $tasks = new TaskList([new InstallComposerDevDependencyTask('ergonomic/effigy', '4')]);

        $this->executor->arePrerequisitesMet($tasks, $this->project, $this->interviewer);
        $this->executor->execute($tasks, $this->project, $this->interviewer);
        $this->executor->rollBack($tasks, $this->project, $this->interviewer);

        $this->composerProject
            ->shouldHaveReceived('restoreConfiguration')
            ->with()
            ->once();
    }

    /** @test */
    public function initialises_a_composer_project_when_none_has_yet_been_initialised()
    {
        $this->composerProject->shouldReceive('isInitialised')->andReturn(false);
        $this->interviewer
            ->shouldReceive('ask')
            ->with(self::yesOrNoQuestionRegarding('initialise one?'))
            ->andReturn(YesOrNoAnswer::yes());

        $tasks = new TaskList([new InstallComposerDevDependencyTask('lefty/loosy', '2')]);

        $this->assertTrue(
            $this->executor->arePrerequisitesMet($tasks, $this->project, $this->interviewer),
            'Nothing should prevent installation of lefty/loosy 2'
        );

        $this->composerProject->shouldHaveReceived('initialise')->once();
    }

    /**
     * @param string $questionSubstring
     * @return MatcherAbstract
     */
    private static function yesOrNoQuestionRegarding($questionSubstring)
    {
        return m::on(
            function (YesOrNoQuestion $question) use ($questionSubstring) {
                return stripos($question->getQuestion(), $questionSubstring) !== false;
            }
        );
    }
}
