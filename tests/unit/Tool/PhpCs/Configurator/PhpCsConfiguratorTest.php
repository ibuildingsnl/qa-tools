<?php

namespace Ibuildings\QaTools\UnitTest\Tool\PhpCs;

use Ibuildings\QaTools\Core\Configuration\TaskDirectory;
use Ibuildings\QaTools\Core\Configuration\TaskHelperSet;
use Ibuildings\QaTools\Core\Interviewer\Answer\Choices;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\AutomatedResponseInterviewer;
use Ibuildings\QaTools\Core\Project\Directory;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Project\ProjectTypeSet;
use Ibuildings\QaTools\Tool\PhpCs\Configurator\PhpCsConfigurator;
use Ibuildings\QaTools\UnitTest\InstallComposerDevDependencyTask;
use Ibuildings\QaTools\UnitTest\WriteFileTask;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase as TestCase;
use Ibuildings\QaTools\UnitTest\ComposerPackage;

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
        $this->interviewer->recordAnswer('What ruleset would you like to use a base?', new Choices([new TextualAnswer('PSR2')]));
        $this->interviewer->recordAnswer('Would you like to allow longer lines than the default? Warn at 120 and fail at 150.', YesOrNoAnswer::yes());
        $this->interviewer->recordAnswer('Would you like be less strict about doc blocks in tests?', YesOrNoAnswer::yes());
        $this->interviewer->recordAnswer('Where are your tests located?', new TextualAnswer('tests/*'));
        $this->interviewer->recordAnswer('Would you like PHPCS to ignore some locations completely?', YesOrNoAnswer::yes());
        $this->interviewer->recordAnswer('Which locations should be ignored?', new TextualAnswer('behat/*'));

        $this->taskHelperSet
            ->shouldReceive('renderTemplate')
            ->with('ruleset.xml.twig', Mockery::any())
            ->andReturn('<?xml version="1.0"?>');

        $configurator = new PhpCsConfigurator();
        $configurator->configure($this->interviewer, $this->taskDirectory, $this->taskHelperSet);

        $this->taskDirectory
            ->shouldHaveReceived('registerTask')
            ->with(InstallComposerDevDependencyTask::forAnyVersionOf('squizlabs/php_codesniffer'))
            ->once();

        $this->taskDirectory
            ->shouldHaveReceived('registerTask')
            ->with(InstallComposerDevDependencyTask::forAnyVersionOf('drupal/coder'))
            ->once();

        $this->taskDirectory
            ->shouldHaveReceived('registerTask')
            ->with(WriteFileTask::equals('./ruleset.xml', '<?xml version="1.0"?>'))
            ->once();

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
