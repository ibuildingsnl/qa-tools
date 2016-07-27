<?php

namespace Fake;

use Ibuildings\QaTools\Core\Interviewer\Answer\Answer;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Interviewer\Question\Question;
use RuntimeException;

final class AutomatedResponseInterviewer implements Interviewer
{
    /** @var Answer[] */
    private $recordedAnswers = [];
    /** @var string[] */
    private $questionsToAnswerToWithDefault = [];

    /**
     * Records an answer that is to be given when a question's text matches the
     * partial question text. The last recorded answer is matched first.
     *
     * @param string $partialQuestionText
     * @param Answer $answer
     * @return void
     */
    public function recordAnswer($partialQuestionText, Answer $answer)
    {
        array_unshift($this->recordedAnswers, [$partialQuestionText, $answer]);
    }

    /**
     * Records that the question's default answer ought to be given when a
     * question's text matches the partial question text.
     *
     * @param string $partialQuestionText
     * @return void
     */
    public function respondWithDefaultAnswerTo($partialQuestionText)
    {
        $this->questionsToAnswerToWithDefault[] = $partialQuestionText;
    }

    public function ask(Question $question)
    {
        foreach ($this->questionsToAnswerToWithDefault as $partialQuestionText) {
            if (strpos($question, $partialQuestionText) !== false) {
                if (!$question->hasDefaultAnswer()) {
                    throw new RuntimeException(sprintf('No default answer available for question "%s"', $question));
                }
                return $question->getDefaultAnswer();
            }
        }
        foreach ($this->recordedAnswers as list($partialQuestionText, $answer)) {
            if (strpos($question, $partialQuestionText) !== false) {
                return $answer;
            }
        }

        throw new RuntimeException(sprintf('No answer recorded for question "%s"', $question));
    }

    public function say($sentence)
    {
    }

    public function warn($sentence)
    {
    }
}
