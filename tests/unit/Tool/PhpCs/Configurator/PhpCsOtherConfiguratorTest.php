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
use Ibuildings\QaTools\Core\Project\ProjectType;
use Ibuildings\QaTools\Core\Project\ProjectTypeSet;
use Ibuildings\QaTools\Tool\PhpCs\Configurator\PhpCsOtherConfigurator;
use Ibuildings\QaTools\UnitTest\InstallComposerDevDependencyTaskMatcher;
use Ibuildings\QaTools\UnitTest\WriteFileTaskMatcher;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * @group Tool
 * @group PhpCs
 */
class PhpCsOtherConfiguratorTest extends TestCase
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
            'Triggered Tomato',
            new Directory('.'),
            new Directory('.'),
            new ProjectTypeSet([new ProjectType(ProjectType::TYPE_PHP_DRUPAL_8)]),
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
        $this->interviewer->recordAnswer('What ruleset would you like to use as a base?', new Choices([new TextualAnswer('PSR2')]));
        $this->interviewer->recordAnswer('How would you like to handle line lengths?', new TextualAnswer('Warn when >120. Fail when >150'));
        $this->interviewer->recordAnswer('Would you like to skip any sniffs regarding the doc blocks in tests?', YesOrNoAnswer::yes());
        $this->interviewer->recordAnswer('Where are the tests located for which doc block sniffs will be disabled?', new TextualAnswer('tests/*'));
        $this->interviewer->recordAnswer('Would you like PHPCS to ignore some locations completely? (you may use a regex to match multiple directories)', YesOrNoAnswer::yes());
        $this->interviewer->recordAnswer('Which locations should be ignored?', new TextualAnswer('behat/*'));

        $this->taskHelperSet
            ->shouldReceive('renderTemplate')
            ->with('ruleset.xml.twig', Mockery::any())
            ->andReturn('<?xml version="1.0"?>');

        $configurator = new PhpCsOtherConfigurator();
        $configurator->configure($this->interviewer, $this->taskDirectory, $this->taskHelperSet);

        $this->taskDirectory
            ->shouldHaveReceived('registerTask')
            ->with(InstallComposerDevDependencyTaskMatcher::forAnyVersionOf('squizlabs/php_codesniffer'))
            ->once();

        $this->taskDirectory
            ->shouldHaveReceived('registerTask')
            ->with(WriteFileTaskMatcher::contains('./ruleset.xml', '<?xml version="1.0"?>'))
            ->once();
    }

    /** @test */
    public function installs_phpcs_without_options()
    {
        $this->interviewer->recordAnswer('Would you like to use PHP Code Sniffer?', YesOrNoAnswer::yes());
        $this->interviewer->recordAnswer('What ruleset would you like to use as a base?', new Choices([new TextualAnswer('PSR2')]));
        $this->interviewer->recordAnswer('How would you like to handle line lengths?', new TextualAnswer('Use base ruleset setting'));
        $this->interviewer->recordAnswer('Would you like to skip any sniffs regarding the doc blocks in tests?', YesOrNoAnswer::no());
        $this->interviewer->recordAnswer('Would you like PHPCS to ignore some locations completely? (you may use a regex to match multiple directories)', YesOrNoAnswer::no());

        $this->taskHelperSet
            ->shouldReceive('renderTemplate')
            ->with('ruleset.xml.twig', Mockery::any())
            ->andReturn('<?xml version="1.0"?>');

        $configurator = new PhpCsOtherConfigurator();
        $configurator->configure($this->interviewer, $this->taskDirectory, $this->taskHelperSet);

        $this->taskDirectory
            ->shouldHaveReceived('registerTask')
            ->with(InstallComposerDevDependencyTaskMatcher::forAnyVersionOf('squizlabs/php_codesniffer'))
            ->once();

        $this->taskDirectory
            ->shouldHaveReceived('registerTask')
            ->with(WriteFileTaskMatcher::contains('./ruleset.xml', '<?xml version="1.0"?>'))
            ->once();
    }

    /** @test */
    public function does_not_install_phpcs_when_not_desired()
    {
        $this->interviewer->recordAnswer('Would you like to use PHP Code Sniffer?', YesOrNoAnswer::no());

        $configurator = new PhpCsOtherConfigurator();
        $configurator->configure($this->interviewer, $this->taskDirectory, $this->taskHelperSet);

        $this->taskDirectory->shouldNotHaveReceived('registerTask');
    }
}
