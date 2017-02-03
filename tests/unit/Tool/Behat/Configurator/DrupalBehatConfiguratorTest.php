<?php

namespace Ibuildings\QaTools\UnitTest\Tool\Behat\Configurator;

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
use Ibuildings\QaTools\Tool\Behat\Configurator\DrupalBehatConfigurator;
use Ibuildings\QaTools\UnitTest\AddBuildTaskMatcher;
use Ibuildings\QaTools\UnitTest\InstallComposerDevDependencyTaskMatcher;
use Ibuildings\QaTools\UnitTest\WriteFileTaskMatcher;
use Mockery;
use PHPUnit_Framework_TestCase;

class DrupalBehatConfiguratorTest extends PHPUnit_Framework_TestCase
{
    const TARGET_NAME = 'behat';

    /**
     * @var AutomatedResponseInterviewer
     */
    private $interviewer;

    /**
     * @var Mockery\MockInterface|TaskHelperSet
     */
    private $taskHelperSet;

    /**
     * @var Mockery\MockInterface|TaskDirectory
     */
    private $taskDirectory;

    protected function setUp()
    {
        $this->interviewer = new AutomatedResponseInterviewer();
        $this->taskHelperSet = Mockery::mock(TaskHelperSet::class);
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
    }

    /**
     * @test
     */
    public function installs_behat_when_desired()
    {
        $this->interviewer->recordAnswer(
            'Would you like to install Behat?',
            YesOrNoAnswer::yes()
        );

        $this->taskHelperSet
            ->shouldReceive('renderTemplate')
            ->with('drupal/behat.yml')
            ->andReturn('default:');
        $this->taskHelperSet
            ->shouldReceive('renderTemplate')
            ->with('drupal/FeatureContext.php.twig')
            ->andReturn('<?php');
        $this->taskHelperSet
            ->shouldReceive('renderTemplate')
            ->with('ant-build.xml.twig', ['targetName' => self::TARGET_NAME])
            ->andReturn('behat-snippet');

        $configurator = new DrupalBehatConfigurator();
        $configurator->configure($this->interviewer, $this->taskDirectory, $this->taskHelperSet);

        $this->taskDirectory->shouldHaveReceived(
            'registerTask',
            [InstallComposerDevDependencyTaskMatcher::forVersionOf('behat/behat', '^3.3')]
        );
        $this->taskDirectory->shouldHaveReceived(
            'registerTask',
            [InstallComposerDevDependencyTaskMatcher::forVersionOf('drupal/drupal-extension', '^3.1')]
        );

        $this->taskDirectory
            ->shouldHaveReceived('registerTask')
            ->with(WriteFileTaskMatcher::contains('./features/bootstrap/FeatureContext.php', '<?php'))
            ->once();
        $this->taskDirectory
            ->shouldHaveReceived('registerTask')
            ->with(WriteFileTaskMatcher::contains('./behat.yml', 'default:'))
            ->once();

        $this->taskDirectory->shouldHaveReceived(
            'registerTask',
            [
                AddBuildTaskMatcher::with(
                    Build::main(),
                    Tool::withIdentifier(self::TARGET_NAME),
                    Snippet::withContentsAndTargetName('behat-snippet', self::TARGET_NAME)
                ),
            ]
        );
    }

    /**
     * @test
     */
    public function does_not_install_behat_when_not_desired()
    {
        $this->interviewer->recordAnswer(
            'Would you like to install Behat?',
            YesOrNoAnswer::no()
        );

        $configurator = new DrupalBehatConfigurator();
        $configurator->configure($this->interviewer, $this->taskDirectory, $this->taskHelperSet);

        $this->taskDirectory->shouldNotHaveReceived('registerTask');
    }
}
