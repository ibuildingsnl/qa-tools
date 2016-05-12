<?php

namespace Ibuildings\QaTools\Core\IO\Cli;

use Ibuildings\QaTools\Exception\InvalidArgumentException;
use Ibuildings\QaTools\Value\Question\ChecklistQuestion;
use Ibuildings\QaTools\Value\Question\MultipleChoiceQuestion;
use Ibuildings\QaTools\Value\Question\TextualQuestion;
use Ibuildings\QaTools\Value\Question\Question;
use Ibuildings\QaTools\Value\Question\YesOrNoQuestion;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question as ConsoleQuestion;

final class ConsoleQuestionFactory
{
    public static function createFrom(Question $question)
    {
        switch (get_class($question)) {
            case TextualQuestion::class:
                /** @var TextualQuestion $question */
                return new ConsoleQuestion(
                    $question->getQuestion(),
                    $question->getDefaultAnswer()->getAnswer()
                );
                break;

            case YesOrNoQuestion::class:
                /** @var YesOrNoQuestion $question */
                return new ConfirmationQuestion($question->getQuestion(), $question->getDefaultAnswer()->getAnswer());
                break;

            case MultipleChoiceQuestion::class:
                /** @var MultipleChoiceQuestion $question */
                return new ChoiceQuestion(
                    $question->getQuestion(),
                    $question->getPossibleChoicesAsStrings(),
                    $question->getDefaultAnswer()
                );
                break;

            case ChecklistQuestion::class:
                /** @var ChecklistQuestion $question */
                $consoleQuestion = new ChoiceQuestion(
                    $question->getQuestion(),
                    $question->getPossibleChoicesAsStrings(),
                    $question->getDefaultChoicesAsString()
                );
                $consoleQuestion->setMultiselect(true);

                return $consoleQuestion;
                break;

            default:
                throw new InvalidArgumentException(
                    sprintf(
                        'Could not map to console question: question of type "%s" not supported',
                        get_class($question)
                    )
                );
                break;
        }
    }
}
