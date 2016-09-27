<?php

namespace Ibuildings\QaTools\UnitTest\Tool\PhpCs;

use Ibuildings\QaTools\Core\Configuration\TaskDirectory;
use Ibuildings\QaTools\Core\Configuration\TaskHelperSet;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\AutomatedResponseInterviewer;
use Ibuildings\QaTools\Core\Project\Directory;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Project\ProjectTypeSet;
use Ibuildings\QaTools\Core\Task\InstallComposerDevDependencyTask;
use Ibuildings\QaTools\Core\Task\Task;
use Ibuildings\QaTools\Core\Task\WriteFileTask;
use Ibuildings\QaTools\Tool\PhpCs\Configurator\PhpCsConfigurator;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * @group Tool
 * @group PhpCs
 */
class PhpCsConfiguratorTest extends TestCase
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
            'Xenophobic Xavier',
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
    public function installs_phpcs_when_desired()
    {
        $this->interviewer->recordAnswer('Would you like to use PHP Code Sniffer?', YesOrNoAnswer::yes());

        $this->taskHelperSet
            ->shouldReceive('renderTemplate')
            ->with('ruleset.xml.twig', Mockery::any())
            ->andReturn('<?xml version="1.0"?>');

        $configurator = new PhpCsConfigurator();
        $configurator->configure($this->interviewer, $this->taskDirectory, $this->taskHelperSet);

        $this->taskDirectory
            ->shouldHaveReceived('registerTask')
            ->with(
                Mockery::on(
                    function (Task $task) {
                        return $task instanceof InstallComposerDevDependencyTask
                            && $task->getPackageName() === 'squizlabs/php_codesniffer';
                    }
                )
            );
        $this->taskDirectory
            ->shouldHaveReceived('registerTask')
            ->with(
                Mockery::on(
                    function (Task $task) {
                        return $task instanceof WriteFileTask
                            && $task->getFilePath() === './ruleset.xml'
                            && $task->getFileContents() === '<?xml version="1.0"?>';
                    }
                )
            );
    }

    /** @test */
    public function does_not_install_phpcs_when_not_desired()
    {
        $this->interviewer->recordAnswer('Would you like to use PHP Code Sniffer?', YesOrNoAnswer::no());

        $configurator = new PhpCsConfigurator();
        $configurator->configure($this->interviewer, $this->taskDirectory, $this->taskHelperSet);

        $this->taskDirectory->shouldNotHaveReceived('registerTask');
    }
}
