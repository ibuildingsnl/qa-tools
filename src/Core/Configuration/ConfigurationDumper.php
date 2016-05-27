<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Interviewer\Answer\Answer;
use Ibuildings\QaTools\Core\Interviewer\Answer\Choices;
use Ibuildings\QaTools\Core\IO\File\FileHandler;
use Ibuildings\QaTools\Core\Project\ProjectType;
use Zend\Json\Json;

final class ConfigurationDumper
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
     * @param Configuration $configuration
     */
    public function dump(Configuration $configuration)
    {
        $project = $configuration->getProject();

        $answers = array_map(
            function (Answer $answer) {
                if ($answer instanceof Choices) {
                    return $answer->convertToArrayOfStrings();
                }

                return $answer->getAnswer();
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
