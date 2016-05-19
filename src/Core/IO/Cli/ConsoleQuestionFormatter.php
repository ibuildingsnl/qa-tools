<?php

namespace Ibuildings\QaTools\Core\IO\Cli;

use Ibuildings\QaTools\Core\Interviewer\Question\ChecklistQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\MultipleChoiceQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\TextualQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\YesOrNoQuestion;

class ConsoleQuestionFormatter
{
    const QUESTION_FORMAT       = '<info>%s</info>';
    const DEFAULT_ANSWER_FORMAT = ' <comment>[%s]</comment>';

    /**
     * @param TextualQuestion $question
     * @return string
     */
    public function formatTextualQuestion(TextualQuestion $question)
    {
        $defaultAnswer = '';
        if ($question->hasDefaultAnswer()) {
            $defaultAnswer = sprintf(self::DEFAULT_ANSWER_FORMAT, $question->getDefaultAnswer()->getAnswer());
        }

        return sprintf(self::QUESTION_FORMAT . $defaultAnswer . PHP_EOL . ' > ', $question->getQuestion());
    }

    /**
     * @param YesOrNoQuestion $question
     * @return string
     */
    public function formatYesOrNoQuestion(YesOrNoQuestion $question)
    {
        $defaultAnswerValue = 'y/n';
        if ($question->hasDefaultAnswer()) {
            $defaultAnswerValue = $question->getDefaultAnswer()->isYes() ? 'Y/n' : 'y/N';
        }

        $defaultAnswer = sprintf(self::DEFAULT_ANSWER_FORMAT, $defaultAnswerValue);

        return sprintf(self::QUESTION_FORMAT . $defaultAnswer . PHP_EOL . ' > ', $question->getQuestion());
    }

    /**
     * @param MultipleChoiceQuestion $question
     * @return string
     */
    public function formatMultipleChoiceQuestion(MultipleChoiceQuestion $question)
    {
        $defaultAnswer = '';
        if ($question->hasDefaultAnswer()) {
            $defaultAnswer = sprintf(self::DEFAULT_ANSWER_FORMAT, $question->getDefaultAnswer()->convertToString());
        }

        return sprintf(self::QUESTION_FORMAT . $defaultAnswer, $question->getQuestion());
    }

    /**
     * @param ChecklistQuestion $question
     * @return string
     */
    public function formatChecklistQuestion(ChecklistQuestion $question)
    {
        $defaultAnswer = '';
        if ($question->hasDefaultAnswer()) {
            $defaultAnswer = sprintf(self::DEFAULT_ANSWER_FORMAT, $question->getDefaultAnswer()->convertToString());
        }

        return sprintf(self::QUESTION_FORMAT . $defaultAnswer, $question->getQuestion());
    }
}
