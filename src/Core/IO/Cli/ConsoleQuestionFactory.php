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
    /**
     * @var ConsoleQuestionFormatter
     */
    private $consoleQuestionFormatter;

    public function __construct(ConsoleQuestionFormatter $consoleQuestionFormatter)
    {
        $this->consoleQuestionFormatter = $consoleQuestionFormatter;
    }

    public function createFrom(Question $question)
    {
        switch (get_class($question)) {
            case TextualQuestion::class:
                /** @var TextualQuestion $question */
                return new ConsoleQuestion(
                    $this->consoleQuestionFormatter->formatTextualQuestion($question),
                    $question->getDefaultAnswerAsString()
                );
                break;

            case YesOrNoQuestion::class:
                /** @var YesOrNoQuestion $question */
                return new ConfirmationQuestion(
                    $this->consoleQuestionFormatter->formatYesOrNoQuestion($question),
                    $question->getDefaultAnswerAsString());
                break;

            case MultipleChoiceQuestion::class:
                /** @var MultipleChoiceQuestion $question */
                return new ChoiceQuestion(
                    $this->consoleQuestionFormatter->formatMultipleChoiceQuestion($question),
                    $question->getPossibleChoicesAsStrings(),
                    $question->getDefaultAnswerAsString()
                );
                break;

            case ChecklistQuestion::class:
                /** @var ChecklistQuestion $question */
                $consoleQuestion = new ChoiceQuestion(
                    $this->consoleQuestionFormatter->formatChecklistQuestion($question),
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
