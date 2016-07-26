<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Fake\AutomatedResponseInterviewer;
use Fake\InMemoryConfigurationRepository;
use Ibuildings\QaTools\Core\Configuration\ProjectConfigurator;
use Ibuildings\QaTools\Core\Configuration\RunListConfigurator;
use Ibuildings\QaTools\Core\Configuration\TaskDirectoryFactory;
use Ibuildings\QaTools\Core\Configuration\TaskHelperSet;
use Ibuildings\QaTools\Core\Configurator\ConfiguratorRepository;
use Ibuildings\QaTools\Core\Interviewer\Answer\Factory\AnswerFactory;
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
            AnswerFactory::createFrom('./')
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
     * @When I enable Travis
     */
    public function iEnableTravis()
    {
        $this->configuredTravisEnabled = true;
        $this->interviewer->recordAnswer(
            'Would you like to integrate Travis in your project?',
            AnswerFactory::createFrom(true)
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

        Assert::assertTrue(
            $configuredProject->equals($expectedProject),
            'Configured project is not according to expectations'
        );
    }
}
