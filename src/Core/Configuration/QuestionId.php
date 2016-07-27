<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Interviewer\Question\Question;

final class QuestionId
{
    /**
     * @var string
     */
    private $questionId;

    /**
     * @param string   $scope
     * @param Question $question
     * @return QuestionId
     */
    public static function fromScopeAndQuestion($scope, Question $question)
    {
        Assertion::nonEmptyString($scope, 'Scope ought to be a non-empty string, got "%s" of type "%s"');

        return new QuestionId(md5($scope . ':' . $question->getQuestion()));
    }

    /**
     * @param string $questionId
     */
    public function __construct($questionId)
    {
        Assertion::nonEmptyString($questionId, 'Question ID ought to be a non-empty string, got "%s" of type "%s"');

        $this->questionId = $questionId;
    }

    /**
     * @param QuestionId $other
     * @return bool
     */
    public function equals(QuestionId $other)
    {
        return $this->questionId === $other->questionId;
    }

    /**
     * @return string
     */
    public function getQuestionId()
    {
        return $this->questionId;
    }

    public function __toString()
    {
        return sprintf('%s("%s")', self::class, $this->questionId);
    }
}
