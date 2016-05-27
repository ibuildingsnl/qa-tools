<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Interviewer\Answer\Factory\AnswerFactory;
use Ibuildings\QaTools\Core\IO\File\FileHandler;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Project\ProjectType;
use Zend\Json\Json;

final class ConfigurationLoader
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

    public function configurationExists()
    {
        return $this->fileHandler->exists($this->filePath);
    }

    public function load()
    {
        $jsonData = Json::decode($this->fileHandler->readFrom($this->filePath), Json::TYPE_ARRAY);

        Assertion::keyExists($jsonData, 'projectName');
        Assertion::keyExists($jsonData, 'configurationFilesLocation');
        Assertion::keyExists($jsonData, 'projectTypes');
        Assertion::keyExists($jsonData, 'travisEnabled');
        Assertion::keyExists($jsonData, 'answers');

        return new Configuration(
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
}
