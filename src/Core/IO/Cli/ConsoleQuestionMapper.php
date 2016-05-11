<?php

namespace Ibuildings\QaTools\Core\IO\Cli;

use Ibuildings\QaTools\Exception\InvalidArgumentException;
use Ibuildings\QaTools\Value\Answer\Answer;
use Ibuildings\QaTools\Value\Question\ChecklistQuestion;
use Ibuildings\QaTools\Value\Question\MultipleChoiceQuestion;
use Ibuildings\QaTools\Value\Question\OpenQuestion;
use Ibuildings\QaTools\Value\Question\Question;
use Ibuildings\QaTools\Value\Question\YesNoQuestion;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question as ConsoleQuestion;

final class ConsoleQuestionMapper
{
    public static function mapToConsoleQuestion(Question $question)
    {
        if ($question instanceof OpenQuestion) {
            return self::mapToQuestion($question);
        }

        if ($question instanceof YesNoQuestion) {
            return self::mapToConfirmationQuestion($question);
        }

        if ($question instanceof MultipleChoiceQuestion) {
            return self::mapToChoiceQuestion($question);
        }

        if ($question instanceof ChecklistQuestion) {
            return self::mapToMultiSelectChoiceQuestion($question);
        }

        throw new InvalidArgumentException(
            sprintf(
                'Could not map to console question: "%s" not supported',
                get_class($question)
            )
        );
    }

    /**
     * @param OpenQuestion $question
     * @return ConsoleQuestion
     */
    public static function mapToQuestion(OpenQuestion $question)
    {
        return new ConsoleQuestion($question->getQuestion(), $question->getDefaultAnswer()->getAnswer());
    }

    /**
     * @param YesNoQuestion $question
     * @return ConfirmationQuestion
     */
    public static function mapToConfirmationQuestion(YesNoQuestion $question)
    {
        return new ConfirmationQuestion($question->getQuestion(), $question->getDefaultAnswer()->getAnswer());
    }

    /**
     * @param MultipleChoiceQuestion $question
     * @return ChoiceQuestion
     */
    public static function mapToChoiceQuestion(MultipleChoiceQuestion $question)
    {
        $choices = array_map(
            function (Answer $answer) {
                return $answer->getAnswer();
            },
            iterator_to_array(
                $question->getPossibleAnswers()
            )
        );

        return new ChoiceQuestion(
            $question->getQuestion(),
            $choices,
            $question->getDefaultAnswer()
        );
    }

    /**
     * @param ChecklistQuestion $question
     * @return ChoiceQuestion
     */
    public static function mapToMultiSelectChoiceQuestion(ChecklistQuestion $question)
    {
        $choices = array_map(
            function (Answer $answer) {
                return $answer->getAnswer();
            },
            iterator_to_array($question->getPossibleAnswers())
        );

        $defaultAnswers = implode(
            ',',
            array_map(
                function (Answer $answer) {
                    return $answer->getAnswer();
                },
                iterator_to_array($question->getDefaultAnswer())
            )
        );

        $consoleQuestion = new ChoiceQuestion(
            $question->getQuestion(),
            $choices,
            $defaultAnswers
        );
        $consoleQuestion->setMultiselect(true);

        return $consoleQuestion;
    }
}
