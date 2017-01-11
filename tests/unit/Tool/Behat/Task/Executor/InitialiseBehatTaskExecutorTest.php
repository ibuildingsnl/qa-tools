<?php

namespace Ibuildings\QaTools\UnitTest\Tool\Behat\Task\Executor;

use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Project\Directory;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Task\TaskList;
use Ibuildings\QaTools\Tool\Behat\Initialiser\BehatInitialiser;
use Ibuildings\QaTools\Tool\Behat\Task\Executor\InitialiseBehatTaskExecutor;
use Ibuildings\QaTools\Tool\Behat\Task\InitialiseBehatTask;
use Mockery;
use PHPUnit_Framework_TestCase;

class InitialiseBehatTaskExecutorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mockery\MockInterface|Project
     */
    private $project;

    /**
     * @var Mockery\MockInterface|Interviewer
     */
    private $interviewer;

    /**
     * @var Mockery\MockInterface|BehatInitialiser
     */
    private $initialiser;

    protected function setUp()
    {
        $this->project = Mockery::mock(Project::class);
        $this->interviewer = Mockery::spy(Interviewer::class);
        $this->initialiser = Mockery::spy(BehatInitialiser::class);
    }

    /**
     * @test
     */
    public function should_support_initialise_behat_task()
    {
        $executor = new InitialiseBehatTaskExecutor($this->initialiser);

        $this->assertTrue($executor->supports(new InitialiseBehatTask()));
    }

    /**
     * @test
     */
    public function should_report_prerequisites_met()
    {
        $executor = new InitialiseBehatTaskExecutor($this->initialiser);

        $this->assertTrue(
            $executor->arePrerequisitesMet(
                new TaskList([new InitialiseBehatTask()]),
                $this->project,
                $this->interviewer
            )
        );
    }

    /**
     * @test
     */
    public function should_initialise_behat()
    {
        $executor = new InitialiseBehatTaskExecutor($this->initialiser);

        $directory = new Directory(getcwd());

        $this->project->shouldReceive('getRootDirectory')->andReturn($directory);

        $executor->execute(
            new TaskList([new InitialiseBehatTask()]),
            $this->project,
            $this->interviewer
        );

        $this->interviewer->shouldHaveReceived('notice');
        $this->initialiser->shouldHaveReceived('initialise', [$directory]);
    }
}
