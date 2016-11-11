<?php

namespace Ibuildings\QaTools\UnitTest\Tool\Phplint;

use Ibuildings\QaTools\Core\Build\Build;
use Ibuildings\QaTools\Core\Build\Snippet;
use Ibuildings\QaTools\Core\Build\Tool;
use Ibuildings\QaTools\Core\Configuration\TaskDirectory;
use Ibuildings\QaTools\Core\Configuration\TaskHelperSet;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\AutomatedResponseInterviewer;
use Ibuildings\QaTools\Core\Project\Directory;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Project\ProjectTypeSet;
use Ibuildings\QaTools\Tool\PhpLint\Configurator\PhpLintConfigurator;
use Ibuildings\QaTools\Tool\PhpLint\PhpLint;
use Ibuildings\QaTools\UnitTest\AddBuildTaskMatcher;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * @group Tool
 * @group Phplint
 */
class PhpLintConfiguratorTest extends TestCase
{
    /** @var AutomatedResponseInterviewer */
    private $interviewer;
    /** @var TaskDirectory|MockInterface */
    private $taskDirectory;
    /** @var TaskHelperSet|MockInterface */
    private $taskHelperSet;

    protected function setUp()
    {
        $this->interviewer = new AutomatedResponseInterviewer();
        $this->project = new Project(
            'Innocuous Insulation',
            new Directory('.'),
            new Directory('.'),
            new ProjectTypeSet(),
            false
        );
        $this->taskDirectory = Mockery::spy(TaskDirectory::class);
        $this->taskDirectory->shouldReceive('getProject')->andReturn($this->project);
        $this->taskHelperSet = Mockery::mock(TaskHelperSet::class);
    }

    /** @test */
    public function installs_phplint_when_desired()
    {
        $this->interviewer->recordAnswer('Would you like to use PHP Lint?', YesOrNoAnswer::yes());

        $this->taskHelperSet
            ->shouldReceive('renderTemplate')
            ->with('ant-full.xml.twig', ['targetName' => PhpLint::ANT_TARGET_FULL])
            ->andReturn('php-lint-full-template')
            ->once();

        $this->taskHelperSet
            ->shouldReceive('renderTemplate')
            ->with('ant-diff.xml.twig', ['targetName' => PhpLint::ANT_TARGET_DIFF])
            ->andReturn('php-lint-diff-template')
            ->once();

        $configurator = new PhpLintConfigurator();
        $configurator->configure($this->interviewer, $this->taskDirectory, $this->taskHelperSet);

        $this->taskDirectory
            ->shouldHaveReceived('registerTask')
            ->with(AddBuildTaskMatcher::with(
                Build::main(),
                Tool::withIdentifier('phplint'),
                Snippet::withContentsAndTargetName('php-lint-full-template', PhpLint::ANT_TARGET_FULL)
            ));

        $this->taskDirectory
            ->shouldHaveReceived('registerTask')
            ->with(AddBuildTaskMatcher::with(
                Build::preCommit(),
                Tool::withIdentifier('phplint'),
                Snippet::withContentsAndTargetName('php-lint-diff-template', PhpLint::ANT_TARGET_DIFF)
            ));

    }

    /** @test */
    public function does_not_install_phplint_when_not_desired()
    {
        $this->interviewer->recordAnswer('Would you like to use PHP Lint?', YesOrNoAnswer::no());

        $configurator = new PhpLintConfigurator();
        $configurator->configure($this->interviewer, $this->taskDirectory, $this->taskHelperSet);

        $this->taskDirectory->shouldNotHaveReceived('registerTask');
    }
}
