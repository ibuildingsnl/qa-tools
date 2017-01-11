<?php

namespace Ibuildings\QaTools\UnitTest\Tool\PhpUnit\Configurator;

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
use Ibuildings\QaTools\Tool\PhpUnit\Configurator\PhpUnitConfigurator;
use Ibuildings\QaTools\UnitTest\AddBuildTaskMatcher;
use Ibuildings\QaTools\UnitTest\InstallComposerDevDependencyTaskMatcher;
use Mockery;
use PHPUnit_Framework_TestCase;

class PhpUnitConfiguratorTest extends PHPUnit_Framework_TestCase
{
    const TARGET_NAME = 'phpunit';

    /**
     * @var AutomatedResponseInterviewer
     */
    private $interviewer;

    /**
     * @var Mockery\MockInterface|TaskDirectory
     */
    private $taskDirectory;

    /**
     * @var Mockery\MockInterface|TaskHelperSet
     */
    private $taskHelperSet;

    protected function setUp()
    {
        $this->interviewer = new AutomatedResponseInterviewer();

        $this->taskDirectory = Mockery::spy(TaskDirectory::class);
        $this->taskDirectory->shouldReceive('getProject')->andReturn(
            new Project(
                'Xenophobic Xavier',
                new Directory('.'),
                new Directory('.'),
                new ProjectTypeSet(),
                false
            )
        );
        $this->taskHelperSet = Mockery::mock(TaskHelperSet::class);
    }

    /**
     * @test
     */
    public function installs_phpunit_when_desired()
    {
        $this->interviewer->recordAnswer(
            'Would you like to run automated tests with PHPUnit?',
            YesOrNoAnswer::yes()
        );

        $this->taskHelperSet
            ->shouldReceive('renderTemplate')
            ->with('ant-build.xml.twig', ['targetName' => self::TARGET_NAME])
            ->andReturn('phpunit-snippet');

        $configurator = new PhpUnitConfigurator();
        $configurator->configure($this->interviewer, $this->taskDirectory, $this->taskHelperSet);

        $this->taskDirectory->shouldHaveReceived(
            'registerTask',
            [InstallComposerDevDependencyTaskMatcher::forVersionOf('phpunit/phpunit', '^5.7')]
        );

        $this->taskDirectory->shouldHaveReceived(
            'registerTask',
            [
                AddBuildTaskMatcher::with(
                    Build::main(),
                    Tool::withIdentifier(self::TARGET_NAME),
                    Snippet::withContentsAndTargetName('phpunit-snippet', self::TARGET_NAME)
                ),
            ]
        );
    }

    /**
     * @test
     */
    public function does_not_install_phpunit_when_not_desired()
    {
        $this->interviewer->recordAnswer(
            'Would you like to run automated tests with PHPUnit?',
            YesOrNoAnswer::no()
        );

        $configurator = new PhpUnitConfigurator();
        $configurator->configure($this->interviewer, $this->taskDirectory, $this->taskHelperSet);

        $this->taskDirectory->shouldNotHaveReceived('registerTask');
    }
}
