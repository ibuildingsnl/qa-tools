<?php

namespace Ibuildings\QaTools\Core\Interviewer;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Interviewer\Answer\Answer;
use Ibuildings\QaTools\Core\Interviewer\Question\Question;
use Ibuildings\QaTools\Core\IO\File\FileHandler;
use Ibuildings\QaTools\Core\Project\ProjectConfigurator;

final class MemorizingInterviewer implements Interviewer
{
    /**
     * @var FileHandler
     */
    private $fileHandler;

    /**
     * @var Interviewer
     */
    private $interviewer;

    /**
     * @var Answer[]
     */
    private $previousAnswers;

    /**
     * @var Answer[]
     */
    private $givenAnswers = [];

    /**
     * @var string
     */
    private $scope = ProjectConfigurator::class;

    public function __construct(FileHandler $fileHandler, Interviewer $interviewer)
    {
        $this->interviewer = $interviewer;
        $this->fileHandler = $fileHandler;
    }

    /**
     * @param string $scope
     */
    public function setScope($scope)
    {
        Assertion::string($scope);

        $this->scope = $scope;
    }

    public function ask(Question $question)
    {
        $this->ensurePreviousAnswersAreLoaded();

        $questionIdentifier = md5($question.$this->scope);

        if (isset($this->previousAnswers[$questionIdentifier])) {
            $answer   = $this->previousAnswers[$questionIdentifier];
            $question = $question->withDefaultAnswer($answer);
        }

        $givenAnswer = $this->interviewer->ask($question);

        $this->givenAnswers[$questionIdentifier] = $givenAnswer;

        return $givenAnswer;
    }

    public function say($sentence)
    {
        $this->interviewer->say($sentence);
    }

    public function warn($sentence)
    {
        $this->interviewer->warn($sentence);
    }

    private function ensurePreviousAnswersAreLoaded()
    {
        if (isset($this->previousAnswers)) {
            return;
        }

        // @todo: For now this suffices, will be reworked to configuration object and handler
        $configurationData = $this->fileHandler->readFrom('./qa_tools.json');
        $parsedConfigurationData = json_decode($configurationData, true);

        if (array_key_exists('answers', $parsedConfigurationData)) {
            $this->previousAnswers = $parsedConfigurationData['answers'];

            return;
        }

        $this->previousAnswers = [];
    }
}
