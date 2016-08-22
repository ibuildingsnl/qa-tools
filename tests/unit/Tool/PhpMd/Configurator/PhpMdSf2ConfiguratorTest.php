<?php

namespace Ibuildings\QaTools\UnitTest\Tool\PhpMd;

use Ibuildings\QaTools\Core\Configuration\TaskDirectory;
use Ibuildings\QaTools\Core\Configuration\TaskHelperSet;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\AutomatedResponseInterviewer;
use Ibuildings\QaTools\Core\Task\ComposerDevDependencyTask;
use Ibuildings\QaTools\Core\Task\Task;
use Ibuildings\QaTools\Tool\PhpMd\Configurator\PhpMdSf2Configurator;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * @group Tool
 * @group PhpMd
 */
class PhpMdSf2ConfiguratorTest extends TestCase
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
        $this->taskDirectory = Mockery::spy(TaskDirectory::class);
        $this->taskHelperSet = Mockery::mock(TaskHelperSet::class);
    }

    /** @test */
    public function installs_phpmd_when_desired()
    {
        $this->interviewer->recordAnswer('Would you like to use PHP Mess Detector?', YesOrNoAnswer::yes());

        $configurator = new PhpMdSf2Configurator();
        $configurator->configure($this->interviewer, $this->taskDirectory, $this->taskHelperSet);

        $this->taskDirectory
            ->shouldHaveReceived('registerTask')
            ->with(
                Mockery::on(
                    function (Task $task) {
                        return $task instanceof ComposerDevDependencyTask
                            && $task->getPackage()->getName()->getName() === 'phpmd/phpmd';
                    }
                )
            );
    }

    /** @test */
    public function does_not_install_phpmd_when_not_desired()
    {
        $this->interviewer->recordAnswer('Would you like to use PHP Mess Detector?', YesOrNoAnswer::no());

        $configurator = new PhpMdSf2Configurator();
        $configurator->configure($this->interviewer, $this->taskDirectory, $this->taskHelperSet);

        $this->taskDirectory->shouldNotHaveReceived('registerTask');
    }
}
