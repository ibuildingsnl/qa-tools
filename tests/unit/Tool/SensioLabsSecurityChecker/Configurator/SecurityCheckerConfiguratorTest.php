<?php

namespace Ibuildings\QaTools\UnitTest\Tool\SensioLabsSecurityChecker\Configurator;

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
use Ibuildings\QaTools\Tool\SensioLabsSecurityChecker\Configurator\SecurityCheckerConfigurator;
use Ibuildings\QaTools\UnitTest\AddBuildTaskMatcher;
use Ibuildings\QaTools\UnitTest\InstallComposerDevDependencyTaskMatcher;
use Mockery;
use Mockery\MockInterface;
use PHPUnit_Framework_TestCase;

class SecurityCheckerConfiguratorTest extends PHPUnit_Framework_TestCase
{
    const TARGET_NAME = 'security-checker';

    /**
     * @var AutomatedResponseInterviewer
     */
    private $interviewer;

    /**
     * @var MockInterface|TaskDirectory
     */
    private $taskDirectory;

    /**
     * @var MockInterface|TaskHelperSet
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
    public function installs_security_checker_when_desired()
    {
        $this->interviewer->recordAnswer(
            'Would you like to check for vulnerable dependencies using SensioLabs Security Checker?',
            YesOrNoAnswer::yes()
        );

        $this->taskHelperSet
            ->shouldReceive('renderTemplate')
            ->with('ant-build.xml.twig', ['targetName' => self::TARGET_NAME])
            ->andReturn('security-checker-snippet');

        $configurator = new SecurityCheckerConfigurator();
        $configurator->configure($this->interviewer, $this->taskDirectory, $this->taskHelperSet);

        $this->taskDirectory->shouldHaveReceived(
            'registerTask',
            [InstallComposerDevDependencyTaskMatcher::forVersionOf('sensiolabs/security-checker', '^3.0')]
        );

        $this->taskDirectory->shouldHaveReceived(
            'registerTask',
            [
                AddBuildTaskMatcher::with(
                    Build::main(),
                    Tool::withIdentifier(self::TARGET_NAME),
                    Snippet::withContentsAndTargetName('security-checker-snippet', self::TARGET_NAME)
                ),
            ]
        );
    }

    /**
     * @test
     */
    public function does_not_install_security_checker_when_not_desired()
    {
        $this->interviewer->recordAnswer(
            'Would you like to check for vulnerable dependencies using SensioLabs Security Checker?',
            YesOrNoAnswer::no()
        );

        $configurator = new SecurityCheckerConfigurator();
        $configurator->configure($this->interviewer, $this->taskDirectory, $this->taskHelperSet);

        $this->taskDirectory->shouldNotHaveReceived('registerTask');
    }
}
