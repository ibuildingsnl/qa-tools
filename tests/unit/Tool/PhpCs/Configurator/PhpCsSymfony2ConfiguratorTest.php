<?php

namespace Ibuildings\QaTools\UnitTest\Tool\PhpCs;

use Ibuildings\QaTools\Core\Build\Build;
use Ibuildings\QaTools\Core\Build\Snippet;
use Ibuildings\QaTools\Core\Build\Tool;
use Ibuildings\QaTools\Core\Configuration\TaskDirectory;
use Ibuildings\QaTools\Core\Configuration\TaskHelperSet;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\AutomatedResponseInterviewer;
use Ibuildings\QaTools\Core\Project\Directory;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Project\ProjectType;
use Ibuildings\QaTools\Core\Project\ProjectTypeSet;
use Ibuildings\QaTools\Tool\PhpCs\Configurator\PhpCsSymfony2Configurator;
use Ibuildings\QaTools\Tool\PhpCs\PhpCs;
use Ibuildings\QaTools\UnitTest\AddBuildTaskMatcher;
use Ibuildings\QaTools\UnitTest\InstallComposerDevDependencyTaskMatcher;
use Ibuildings\QaTools\UnitTest\WriteFileTaskMatcher;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * @group Tool
 * @group PhpCs
 */
class PhpCsSymfony2ConfiguratorTest extends TestCase
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
            'Fluent Fisherman',
            new Directory('.'),
            new Directory('.'),
            new ProjectTypeSet([new ProjectType(ProjectType::TYPE_PHP_SF_2)]),
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
            ->with('ruleset-reference.xml.twig', Mockery::any())
            ->andReturn('<?xml version="1.0"?>')
            ->once();

        $this->taskHelperSet
            ->shouldReceive('renderTemplate')
            ->with('ant-build.xml.twig', ['targetName' => PhpCs::ANT_TARGET])
            ->andReturn('snippet')
            ->once();

        $configurator = new PhpCsSymfony2Configurator();
        $configurator->configure($this->interviewer, $this->taskDirectory, $this->taskHelperSet);

        $this->taskDirectory
            ->shouldHaveReceived('registerTask')
            ->with(InstallComposerDevDependencyTaskMatcher::forAnyVersionOf('squizlabs/php_codesniffer'))
            ->once();

        $this->taskDirectory
            ->shouldHaveReceived('registerTask')
            ->with(InstallComposerDevDependencyTaskMatcher::forAnyVersionOf('escapestudios/symfony2-coding-standard'))
            ->once();

        $this->taskDirectory
            ->shouldHaveReceived('registerTask')
            ->with(WriteFileTaskMatcher::contains('./ruleset.xml', '<?xml version="1.0"?>'))
            ->once();

        $this->taskDirectory
            ->shouldHaveReceived('registerTask')
            ->with(AddBuildTaskMatcher::with(
                Build::main(),
                Tool::withIdentifier('phpcs'),
                Snippet::withContentsAndTargetName('snippet', PhpCs::ANT_TARGET))
            );
    }

    /** @test */
    public function installs_phpcs_without_options()
    {
        $this->interviewer->recordAnswer('Would you like to use PHP Code Sniffer?', YesOrNoAnswer::yes());

        $this->taskHelperSet
            ->shouldReceive('renderTemplate')
            ->with('ruleset-reference.xml.twig', Mockery::any())
            ->andReturn('<?xml version="1.0"?>');

        $this->taskHelperSet
            ->shouldReceive('renderTemplate')
            ->with('ant-build.xml.twig', ['targetName' => PhpCs::ANT_TARGET])
            ->andReturn('snippet')
            ->once();

        $configurator = new PhpCsSymfony2Configurator();
        $configurator->configure($this->interviewer, $this->taskDirectory, $this->taskHelperSet);

        $this->taskDirectory
            ->shouldHaveReceived('registerTask')
            ->with(InstallComposerDevDependencyTaskMatcher::forAnyVersionOf('squizlabs/php_codesniffer'))
            ->once();

        $this->taskDirectory
            ->shouldHaveReceived('registerTask')
            ->with(WriteFileTaskMatcher::contains('./ruleset.xml', '<?xml version="1.0"?>'))
            ->once();

        $this->taskDirectory
            ->shouldHaveReceived('registerTask')
            ->with(AddBuildTaskMatcher::with(
                Build::main(),
                Tool::withIdentifier('phpcs'),
                Snippet::withContentsAndTargetName('snippet', PhpCs::ANT_TARGET))
            );
    }

    /** @test */
    public function does_not_install_phpcs_when_not_desired()
    {
        $this->interviewer->recordAnswer('Would you like to use PHP Code Sniffer?', YesOrNoAnswer::no());

        $configurator = new PhpCsSymfony2Configurator();
        $configurator->configure($this->interviewer, $this->taskDirectory, $this->taskHelperSet);

        $this->taskDirectory->shouldNotHaveReceived('registerTask');
    }
}
