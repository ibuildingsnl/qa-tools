<?php

namespace Ibuildings\QaTools\UnitTest\Core\Task\Compiler;

use Ibuildings\QaTools\Core\Composer\Package;
use Ibuildings\QaTools\Core\Composer\PackageSet;
use Ibuildings\QaTools\Core\Composer\Project as ComposerProject;
use Ibuildings\QaTools\Core\Composer\ProjectFactory as ComposerProjectFactory;
use Ibuildings\QaTools\Core\Configuration\RequirementDirectory;
use Ibuildings\QaTools\Core\Interviewer\ScopedInterviewer;
use Ibuildings\QaTools\Core\Project\Directory;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Project\ProjectTypeSet;
use Ibuildings\QaTools\Core\Requirement\ComposerDevDependencyRequirement;
use Ibuildings\QaTools\Core\Requirement\RequirementList;
use Ibuildings\QaTools\Core\Requirement\Specification\TypeSpecification;
use Ibuildings\QaTools\Core\Task\Compiler\ComposerTaskListCompiler;
use Ibuildings\QaTools\Core\Task\InstallComposerDevDependenciesTask;
use Ibuildings\QaTools\Core\Task\TaskList;
use Ibuildings\QaTools\UnitTest\Diffing;
use Ibuildings\QaTools\UnitTest\ValueObject;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * @group Task
 * @group Composer
 */
class ComposerTaskListCompilerTest extends TestCase
{
    use Diffing;

    /** @var ComposerProject */
    private $composerProject;
    /** @var ComposerProjectFactory */
    private $composerProjectFactory;
    /** @var ScopedInterviewer */
    private $interviewer;
    /** @var RequirementDirectory|MockInterface */
    private $requirementsDirectory;

    protected function setUp()
    {
        $this->composerProject = Mockery::mock(ComposerProject::class);
        $this->composerProjectFactory = Mockery::mock(ComposerProjectFactory::class);
        $this->composerProjectFactory->shouldReceive('forDirectory')->with('/prjdir/')->andReturn($this->composerProject);
        $this->interviewer = Mockery::mock(ScopedInterviewer::class)->shouldIgnoreMissing();
        $project = new Project('prj', new Directory('/prjdir'), new Directory('.'), new ProjectTypeSet(), false);
        $this->requirementsDirectory = Mockery::mock(RequirementDirectory::class);
        $this->requirementsDirectory->shouldReceive('getProject')->andReturn($project);
    }

    /** @test */
    public function it_compiles_one_dev_dep_requirement_into_a_single_install_task()
    {
        $specification = ValueObject::equals(new TypeSpecification(ComposerDevDependencyRequirement::class));
        $requirements = new RequirementList(
            [new ComposerDevDependencyRequirement(Package::of('phpunit/phpunit', '18'))]
        );

        $this->requirementsDirectory
            ->shouldReceive('matchRequirements')
            ->with($specification)
            ->andReturn($requirements);

        $compiler = new ComposerTaskListCompiler($this->composerProjectFactory);
        $actualTasks = $compiler->compile($this->requirementsDirectory, $this->interviewer);

        $expectedTasks = new TaskList(
            [new InstallComposerDevDependenciesTask(new PackageSet([Package::of('phpunit/phpunit', '18')]), $this->composerProject)]
        );

        $this->assertTrue(
            $expectedTasks->equals($actualTasks),
            $this->diff($expectedTasks, $actualTasks, 'Task list compiled from requirements differs from expectation')
        );
    }

    /** @test */
    public function it_compiles_two_dev_dep_requirements_into_a_single_install_task()
    {
        $specification = ValueObject::equals(new TypeSpecification(ComposerDevDependencyRequirement::class));

        $packageA = Package::of('a/a', '1');
        $packageB = Package::of('b/b', '2');
        $requirements = new RequirementList([
            new ComposerDevDependencyRequirement($packageA),
            new ComposerDevDependencyRequirement($packageB),
        ]);

        $this->requirementsDirectory
            ->shouldReceive('matchRequirements')
            ->with($specification)
            ->andReturn($requirements);

        $compiler = new ComposerTaskListCompiler($this->composerProjectFactory);
        $actualTasks = $compiler->compile($this->requirementsDirectory, $this->interviewer);

        $expectedTasks = new TaskList(
            [new InstallComposerDevDependenciesTask(new PackageSet([$packageA, $packageB]), $this->composerProject)]
        );

        $this->assertTrue(
            $expectedTasks->equals($actualTasks),
            $this->diff($expectedTasks, $actualTasks, 'Task list compiled from requirements differs from expectation')
        );
    }
}
