<?php

namespace Ibuildings\QaTools\Core\IO\Cli;

use Ibuildings\QaTools\Value\Question\ChecklistQuestion;
use Ibuildings\QaTools\Value\Question\MultipleChoiceQuestion;
use Ibuildings\QaTools\Value\Question\TextualQuestion;
use Ibuildings\QaTools\Value\Question\YesOrNoQuestion;

class ConsoleQuestionFormatter
{
    const QUESTION_FORMAT = '<info>%s</info> <comment>[%s]</comment>';

    public function formatTextualQuestion(TextualQuestion $question)
    {
        return sprintf(self::QUESTION_FORMAT . PHP_EOL . ' > ', $question->getQuestion(), $question->getDefaultAnswerAsString());
    }

    public function formatYesOrNoQuestion(YesOrNoQuestion $question)
    {
        $defaultValue = $question->defaultAnswerIsYes() ? 'Y/n' : 'y/N';

        return sprintf(self::QUESTION_FORMAT . PHP_EOL . ' > ', $question->getQuestion(), $defaultValue);
    }

    public function formatMultipleChoiceQuestion(MultipleChoiceQuestion $question)
    {
        return sprintf(self::QUESTION_FORMAT, $question->getQuestion(), $question->getDefaultAnswerAsString());
    }

    public function formatChecklistQuestion(ChecklistQuestion $question)
    {
        return sprintf(self::QUESTION_FORMAT, $question->getQuestion(), $question->getDefaultChoicesAsString());
    }
}
