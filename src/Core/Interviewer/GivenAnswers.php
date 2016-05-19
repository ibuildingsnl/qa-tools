<?php

namespace Ibuildings\QaTools\Core\Interviewer;

use Ibuildings\QaTools\Core\Exception\LogicException;
use Ibuildings\QaTools\Core\Interviewer\Answer\Answer;
use Ibuildings\QaTools\Core\Interviewer\Answer\Factory\AnswerFactory;
use Ibuildings\QaTools\Core\Interviewer\Question\Question;

final class GivenAnswers
{
    /**
     * @var Answer[]
     */
    private $givenAnswers = [];

    private function __construct(array $givenAnswer = [])
    {
        $this->givenAnswers = $givenAnswer;
    }

    /**
     * @return GivenAnswers
     */
    public static function withoutAnswers()
    {
        return new self([]);
    }

    /**
     * @param Question $question
     * @param InterviewScope $scope
     * @return bool
     */
    public function hasGivenAnswerFor(Question $question, InterviewScope $scope)
    {
        return isset($this->givenAnswers[$question->calculateHash() . $scope->calculateHash()]);
    }

    /**
     * @param Question $question
     * @param InterviewScope $scope
     * @return Answer
     */
    public function getGivenAnswerFor(Question $question, InterviewScope $scope)
    {
        if (!$this->hasGivenAnswerFor($question, $scope)) {
            throw new LogicException(sprintf(
                'Cannot get given answer for question "%s" and scope "%s": no answer given',
                $question,
                $scope->getInterviewScope()
            ));
        }

        return $this->givenAnswers[$question->calculateHash() . $scope->calculateHash()];
    }

    /**
     * @param Question $question
     * @param Answer $answer
     * @param InterviewScope $interviewScope
     */
    public function giveAnswerFor(Question $question, Answer $answer, InterviewScope $interviewScope)
    {
        $this->givenAnswers[$question->calculateHash() . $interviewScope->calculateHash()] = $answer;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return json_encode(
            array_map(
                function (Answer $answer) {
                    return $answer->serialize();
                },
                $this->givenAnswers
            )
        );
    }

    /**
     * @param array $jsonData
     * @return GivenAnswers
     */
    public static function deserialize(array $jsonData)
    {
        return new self(
            array_combine(
                array_keys($jsonData),
                array_map(
                    function ($answer) {
                        return AnswerFactory::createFrom($answer);
                    },
                    $jsonData
                )
            )
        );
    }
}
