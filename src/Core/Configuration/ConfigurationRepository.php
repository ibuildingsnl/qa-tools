<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Interviewer\Answer\Answer;
use Ibuildings\QaTools\Core\Interviewer\Answer\Factory\AnswerFactory;
use Ibuildings\QaTools\Core\IO\File\FileHandler;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Project\ProjectType;
use Zend\Json\Json;

final class ConfigurationRepository
{
    /**
     * @var FileHandler
     */
    private $fileHandler;

    /**
     * @var string
     */
    private $filePath;

    public function __construct(FileHandler $fileHandler, $filePath)
    {
        Assertion::nonEmptyString($filePath, 'filePath');

        $this->fileHandler = $fileHandler;
        $this->filePath = $filePath;
    }

    /**
     * @return bool
     */
    public function configurationExists()
    {
        return $this->fileHandler->exists($this->filePath);
    }

    /**
     * @return Configuration
     */
    public function load()
    {
        $jsonData = Json::decode($this->fileHandler->readFrom($this->filePath), Json::TYPE_ARRAY);

        Assertion::keyExists($jsonData, 'projectName');
        Assertion::keyExists($jsonData, 'configurationFilesLocation');
        Assertion::keyExists($jsonData, 'projectTypes');
        Assertion::keyExists($jsonData, 'travisEnabled');
        Assertion::keyExists($jsonData, 'answers');

        return Configuration::loaded(
            new Project(
                $jsonData['projectName'],
                $jsonData['configurationFilesLocation'],
                array_map(
                    function ($projectType) {
                        return new ProjectType($projectType);
                    },
                    $jsonData['projectTypes']
                ),
                $jsonData['travisEnabled']
            ),
            array_map(
                function ($answer) {
                    return AnswerFactory::createFrom($answer);
                },
                $jsonData['answers']
            )
        );
    }

    /**
     * @param Configuration $configuration
     */
    public function save(Configuration $configuration)
    {
        $project = $configuration->getProject();

        $answers = array_map(
            function (Answer $answer) {
                return $answer->getRaw();
            },
            $configuration->getAnswers()
        );

        $json = Json::encode(
            [
                'projectName'                => $project->getName(),
                'configurationFilesLocation' => $project->getConfigurationFilesLocation(),
                'projectTypes'               => array_map(
                    function (ProjectType $projectType) {
                        return $projectType->getProjectType();
                    },
                    $project->getProjectTypes()
                ),
                'travisEnabled'              => $project->isTravisEnabled(),
                'answers'                    => $answers,
            ]
        );

        $this->fileHandler->writeTo(Json::prettyPrint($json), $this->filePath);
    }
}
