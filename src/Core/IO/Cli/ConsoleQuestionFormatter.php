<?php

namespace Ibuildings\QaTools\Core\IO\Cli;

use Ibuildings\QaTools\Value\Question\ChecklistQuestion;
use Ibuildings\QaTools\Value\Question\MultipleChoiceQuestion;
use Ibuildings\QaTools\Value\Question\TextualQuestion;
use Ibuildings\QaTools\Value\Question\YesOrNoQuestion;

class ConsoleQuestionFormatter
{
    const QUESTION_FORMAT = '<info>%s</info>';
    const DEFAULT_ANSWER_FORMAT = ' <comment>[%s]</comment>';

    /**
     * @param TextualQuestion $question
     * @return string
     */
    public function formatTextualQuestion(TextualQuestion $question)
    {
        $defaultAnswer = '';
        if ($question->hasDefaultAnswer()) {
            $defaultAnswer = sprintf(self::DEFAULT_ANSWER_FORMAT, $question->getDefaultAnswerValue());
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
            $defaultAnswerValue = $question->isDefaultAnswerYes() ? 'Y/n' : 'y/N';
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
            $defaultAnswer = sprintf(self::DEFAULT_ANSWER_FORMAT, $question->getDefaultAnswerValue());
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
        if ($question->hasDefaultChoices()) {
            $defaultAnswer = sprintf(self::DEFAULT_ANSWER_FORMAT, $question->getDefaultChoiceValues());
        }

        return sprintf(self::QUESTION_FORMAT . $defaultAnswer, $question->getQuestion());
    }
}
