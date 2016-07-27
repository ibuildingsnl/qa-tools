<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Fake\AutomatedResponseInterviewer;
use Fake\InMemoryConfigurationRepository;
use Ibuildings\QaTools\Core\Configuration\Configuration;
use Ibuildings\QaTools\Core\Configuration\ProjectConfigurator;
use Ibuildings\QaTools\Core\Configuration\QuestionId;
use Ibuildings\QaTools\Core\Configuration\RunListConfigurator;
use Ibuildings\QaTools\Core\Configuration\TaskDirectoryFactory;
use Ibuildings\QaTools\Core\Configuration\TaskHelperSet;
use Ibuildings\QaTools\Core\Configurator\ConfiguratorRepository;
use Ibuildings\QaTools\Core\Interviewer\Answer\Factory\AnswerFactory;
use Ibuildings\QaTools\Core\Interviewer\Question;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Project\ProjectType;
use Ibuildings\QaTools\Core\Project\ProjectTypeSet;
use Ibuildings\QaTools\Core\Service\ConfigurationService;
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

    /** @var string */
    private $configuredProjectName;
    /** @var string */
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
        $taskHelperSet = Mockery::mock(TaskHelperSet::class);
        $container = Mockery::mock(ContainerInterface::class);
        $runListConfigurator = new RunListConfigurator($taskHelperSet, $container);
        $this->configuratorRepository = new ConfiguratorRepository();
        $taskDirectoryFactory = new TaskDirectoryFactory();

        $this->interviewer = new AutomatedResponseInterviewer();

        $this->configurationService = new ConfigurationService(
            $this->configurationRepository,
            $projectConfigurator,
            $runListConfigurator,
            $this->configuratorRepository,
            $taskDirectoryFactory
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
        $this->configuredConfigurationFilesLocation = $directory;
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
            $this->configuredConfigurationFilesLocation,
            $this->configuredProjectTypes,
            $this->configuredTravisEnabled
        );

        $this->configurationService->configureProject($this->interviewer);

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
            Question::create("What is the project's name?")
        )->getQuestionId();
        $projectTypeQuestionId = QuestionId::fromScopeAndQuestion(
            ProjectConfigurator::class,
            Question::create('What type of project would you like to configure?')
        )->getQuestionId();
        $phpProjectTypeQuestionId = QuestionId::fromScopeAndQuestion(
            ProjectConfigurator::class,
            Question::create('What type of PHP project would you like to configure?')
        )->getQuestionId();
        $travisEnabledQuestionId = QuestionId::fromScopeAndQuestion(
            ProjectConfigurator::class,
            Question::createYesOrNo('Would you like to integrate Travis in your project?')
        )->getQuestionId();

        $this->configurationRepository = new InMemoryConfigurationRepository();
        $this->configurationRepository->save(
            Configuration::loaded(
                new Project(
                    'Trading Service',
                    './',
                    new ProjectTypeSet([new ProjectType(ProjectType::TYPE_PHP_DRUPAL_7)]),
                    false
                ),
                [
                    $projectNameQuestionId    => AnswerFactory::createFrom('Trading Service'),
                    $projectTypeQuestionId    => AnswerFactory::createFrom('PHP'),
                    $phpProjectTypeQuestionId => AnswerFactory::createFrom('Drupal 7'),
                    $travisEnabledQuestionId  => AnswerFactory::createFrom(false),
                ]
            )
        );

        $projectConfigurator = new ProjectConfigurator();
        $taskHelperSet = Mockery::mock(TaskHelperSet::class);
        $container = Mockery::mock(ContainerInterface::class);
        $runListConfigurator = new RunListConfigurator($taskHelperSet, $container);
        $this->configuratorRepository = new ConfiguratorRepository();
        $taskDirectoryFactory = new TaskDirectoryFactory();

        $this->interviewer = new AutomatedResponseInterviewer();

        $this->configurationService = new ConfigurationService(
            $this->configurationRepository,
            $projectConfigurator,
            $runListConfigurator,
            $this->configuratorRepository,
            $taskDirectoryFactory
        );
    }

    /**
     * @When I keep the project name
     */
    public function iKeepTheProjectName()
    {
        $this->configuredProjectName = $this->configurationRepository->load()->getProject()->getName();
        $this->interviewer->respondWithDefaultAnswerTo("What is the project's name?");
    }

    /**
     * @When I keep the project types
     */
    public function iKeepTheProjectTypes()
    {
        $this->configuredProjectTypes = $this->configurationRepository->load()->getProject()->getProjectTypes();
        $this->interviewer->respondWithDefaultAnswerTo('What type of project would you like to configure?');
        $this->interviewer->respondWithDefaultAnswerTo('What type of PHP project would you like to configure?');
    }

    /**
     * @When I keep Travis disabled
     */
    public function iKeepTravisDisabled()
    {
        $this->configuredTravisEnabled = $this->configurationRepository->load()->getProject()->isTravisEnabled();
        $this->interviewer->respondWithDefaultAnswerTo('Would you like to integrate Travis in your project?');
    }
}
