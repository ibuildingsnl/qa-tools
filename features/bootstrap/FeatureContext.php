<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Ibuildings\QaTools\Core\Composer\PackageName;
use Ibuildings\QaTools\Core\Configuration\Configuration;
use Ibuildings\QaTools\Core\Configuration\ConfigurationService;
use Ibuildings\QaTools\Core\Configuration\InMemoryConfigurationRepository;
use Ibuildings\QaTools\Core\Configuration\InMemoryRequirementDirectoryFactory;
use Ibuildings\QaTools\Core\Configuration\ProjectConfigurator;
use Ibuildings\QaTools\Core\Configuration\QuestionId;
use Ibuildings\QaTools\Core\Configuration\RequirementHelperSet;
use Ibuildings\QaTools\Core\Configuration\ToolConfigurator;
use Ibuildings\QaTools\Core\Configurator\ConfiguratorRepository;
use Ibuildings\QaTools\Core\Interviewer\Answer\AnswerFactory;
use Ibuildings\QaTools\Core\Interviewer\AutomatedResponseInterviewer;
use Ibuildings\QaTools\Core\Interviewer\Question\QuestionFactory;
use Ibuildings\QaTools\Core\Interviewer\ScopedInterviewer;
use Ibuildings\QaTools\Core\Project\Directory;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Project\ProjectType;
use Ibuildings\QaTools\Core\Project\ProjectTypeSet;
use Ibuildings\QaTools\Core\Task\Compiler\ComposerTaskListCompiler;
use Ibuildings\QaTools\Core\Task\Executor\TaskListExecutor;
use Ibuildings\QaTools\Core\Task\Specification\InstallComposerPackageSpecification;
use Ibuildings\QaTools\Core\Task\TaskList;
use Ibuildings\QaTools\Tool\PhpMd\Configurator\PhpMdSf2Configurator;
use Mockery\MockInterface;
use PHPUnit_Framework_Assert as Assert;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    /** @var InMemoryConfigurationRepository */
    private $configurationRepository;
    /** @var ConfiguratorRepository */
    private $configuratorRepository;
    /** @var AutomatedResponseInterviewer */
    private $interviewer;
    /** @var ConfigurationService */
    private $configurationService;
    /** @var ContainerInterface|MockInterface */
    private $container;
    /** @var TaskListExecutor|MockInterface */
    private $taskListExecutor;

    /** @var string */
    private $configuredProjectName;
    /** @var Directory */
    private $configuredConfigurationFilesLocation;
    /** @var ProjectTypeSet */
    private $configuredProjectTypes;
    /** @var bool */
    private $configuredTravisEnabled;

    /**
     * @Given a project whose QA tools have not yet been set up
     */
    public function aProjectWhoseQaToolsHaveNotYetBeenSetUp()
    {
        $this->configurationRepository = new InMemoryConfigurationRepository();
        $projectConfigurator = new ProjectConfigurator();
        $requirementHelperSet = Mockery::mock(RequirementHelperSet::class)->shouldIgnoreMissing();
        $this->container = Mockery::mock(ContainerInterface::class);
        $toolConfigurator = new ToolConfigurator($requirementHelperSet, $this->container);
        $this->configuratorRepository = new ConfiguratorRepository();
        $requirementDirectoryFactory = new InMemoryRequirementDirectoryFactory();
        $taskListCompiler = new ComposerTaskListCompiler();
        $this->taskListExecutor = Mockery::spy(TaskListExecutor::class);

        $this->interviewer = new AutomatedResponseInterviewer();

        $this->configurationService = new ConfigurationService(
            $this->configurationRepository,
            $projectConfigurator,
            $toolConfigurator,
            $this->configuratorRepository,
            $requirementDirectoryFactory,
            $taskListCompiler,
            $this->taskListExecutor
        );
    }

    /**
     * @Given no available QA tools
     */
    public function noAvailableQaTools()
    {
    }

    /**
     * @When I name the project :projectName
     */
    public function iNameTheProject($projectName)
    {
        $this->configuredProjectName = $projectName;
        $this->interviewer->recordAnswer("What is the project's name?", AnswerFactory::createFrom($projectName));
    }

    /**
     * @When I want the QA-related files stored in :directory
     */
    public function iWantTheQARelatedFilesStoredIn($directory)
    {
        $this->configuredConfigurationFilesLocation = new Directory($directory);
        $this->interviewer->recordAnswer(
            'Where would you like to store the generated files?',
            AnswerFactory::createFrom($directory)
        );
    }

    /**
     * @When I state the project type is :projectType
     */
    public function iStateTheProjectTypeIs($projectType)
    {
        $this->interviewer->recordAnswer(
            'What type of project would you like to configure?',
            AnswerFactory::createFrom($projectType)
        );
    }

    /**
     * @When I state the PHP project type is :phpProjectType
     */
    public function iStateThePHPProjectTypeIs($phpProjectType)
    {
        $this->configuredProjectTypes = new ProjectTypeSet([ProjectType::fromHumanReadableString($phpProjectType)]);
        $this->interviewer->recordAnswer(
            'What type of PHP project would you like to configure?',
            AnswerFactory::createFrom($phpProjectType)
        );
    }

    /**
     * @When I disable Travis
     */
    public function iDisableTravis()
    {
        $this->configuredTravisEnabled = false;
        $this->interviewer->recordAnswer(
            'Would you like to integrate Travis in your project?',
            AnswerFactory::createFrom(false)
        );
    }

    /**
     * @Then I have a project configured accordingly
     */
    public function iHaveAProjectConfiguredAccordingly()
    {
        $expectedProject = new Project(
            $this->configuredProjectName,
            new Directory('.'),
            $this->configuredConfigurationFilesLocation,
            $this->configuredProjectTypes,
            $this->configuredTravisEnabled
        );

        $this->configurationService->configureProject($this->interviewer, new Directory('.'));

        $configuration = $this->configurationRepository->load();
        $configuredProject = $configuration->getProject();

        if (!$configuredProject->equals($expectedProject)) {
            // Display diff.
            Assert::assertEquals(
                $expectedProject,
                $configuredProject,
                'Configured project is not according to expectations'
            );
        }
    }

    /**
     * @Given the Trading Service project
     */
    public function theTradingServiceProject()
    {
        $projectNameQuestionId = QuestionId::fromScopeAndQuestion(
            ProjectConfigurator::class,
            QuestionFactory::create("What is the project's name?")
        )->getQuestionId();
        $projectTypeQuestionId = QuestionId::fromScopeAndQuestion(
            ProjectConfigurator::class,
            QuestionFactory::create('What type of project would you like to configure?')
        )->getQuestionId();
        $phpProjectTypeQuestionId = QuestionId::fromScopeAndQuestion(
            ProjectConfigurator::class,
            QuestionFactory::create('What type of PHP project would you like to configure?')
        )->getQuestionId();
        $travisEnabledQuestionId = QuestionId::fromScopeAndQuestion(
            ProjectConfigurator::class,
            QuestionFactory::createYesOrNo('Would you like to integrate Travis in your project?')
        )->getQuestionId();

        $this->configurationRepository = new InMemoryConfigurationRepository();
        $this->configurationRepository->save(
            Configuration::loaded(
                new Project(
                    'Trading Service',
                    new Directory('.'),
                    new Directory('./'),
                    new ProjectTypeSet([new ProjectType(ProjectType::TYPE_PHP_SF_2)]),
                    false
                ),
                [
                    $projectNameQuestionId    => AnswerFactory::createFrom('Trading Service'),
                    $projectTypeQuestionId    => AnswerFactory::createFrom('PHP'),
                    $phpProjectTypeQuestionId => AnswerFactory::createFrom('Symfony 2'),
                    $travisEnabledQuestionId  => AnswerFactory::createFrom(false),
                ]
            )
        );

        $projectConfigurator = new ProjectConfigurator();
        $requirementHelperSet = Mockery::mock(RequirementHelperSet::class)->shouldIgnoreMissing();
        $this->container = Mockery::mock(ContainerInterface::class);
        $toolConfigurator = new ToolConfigurator($requirementHelperSet, $this->container);
        $this->configuratorRepository = new ConfiguratorRepository();
        $requirementDirectoryFactory = new InMemoryRequirementDirectoryFactory();
        $taskListCompiler = new ComposerTaskListCompiler();
        $this->taskListExecutor = Mockery::spy(TaskListExecutor::class);

        $this->interviewer = new AutomatedResponseInterviewer();

        $this->configurationService = new ConfigurationService(
            $this->configurationRepository,
            $projectConfigurator,
            $toolConfigurator,
            $this->configuratorRepository,
            $requirementDirectoryFactory,
            $taskListCompiler,
            $this->taskListExecutor
        );

        $this->configuredProjectName = $this->configurationRepository->load()->getProject()->getName();
        $this->configuredProjectTypes = $this->configurationRepository->load()->getProject()->getProjectTypes();
        $this->configuredTravisEnabled = $this->configurationRepository->load()->getProject()->isTravisEnabled();
        $this->configuredConfigurationFilesLocation =
            $this->configurationRepository->load()->getProject()->getConfigurationFilesLocation();
        $this->interviewer->respondWithDefaultAnswerTo("What is the project's name?");
        $this->interviewer->respondWithDefaultAnswerTo('What type of project would you like to configure?');
        $this->interviewer->respondWithDefaultAnswerTo('What type of PHP project would you like to configure?');
        $this->interviewer->respondWithDefaultAnswerTo('Would you like to integrate Travis in your project?');
        $this->interviewer->respondWithDefaultAnswerTo('Where would you like to store the generated files?');
    }

    /**
     * @Given the PHPMD Symfony 2 configurator is available
     */
    public function thePhpMdSymfony2ConfiguratorIsAvailable()
    {
        $this->container->shouldReceive('getParameter')->with('tool.Ibuildings\QaTools\Tool\PhpMd\PhpMd.resource_path')->andReturn('');
        $this->configuratorRepository->add(new PhpMdSf2Configurator(), new ProjectType(ProjectType::TYPE_PHP_SF_2));
    }

    /**
     * @When I want to use PHPMD
     */
    public function iWantToUsePhpmd()
    {
        $this->interviewer->recordAnswer('Would you like to use PHP Mess Detector?', AnswerFactory::createFrom(true));
    }

    /**
     * @When the configuration is complete
     */
    public function theConfigurationIsComplete()
    {
        $this->configurationService->configureProject($this->interviewer, new Directory('.'));
    }

    /**
     * @Then the PHPMD Composer package is installed
     */
    public function thePhpMdComposerPackageIsInstalled()
    {
        $aTaskThatInstallsPhpMd = Mockery::on(
            function (TaskList $tasks) {
                $anyVersionOfPhpMd = InstallComposerPackageSpecification::ofAnyVersion(new PackageName('phpmd/phpmd'));
                $matchingTasks = $tasks->match($anyVersionOfPhpMd);
                Assert::assertCount(1, $matchingTasks);

                return true;
            }
        );

        $this->taskListExecutor
            ->shouldHaveReceived('execute')
            ->with($aTaskThatInstallsPhpMd, Mockery::type(ScopedInterviewer::class))
            ->once();
    }
}
