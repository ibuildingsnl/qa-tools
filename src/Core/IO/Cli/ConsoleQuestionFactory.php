<?php

namespace Ibuildings\QaTools\Core\IO\Cli;

use Ibuildings\QaTools\Exception\InvalidAnswerException;
use Ibuildings\QaTools\Exception\InvalidArgumentException;
use Ibuildings\QaTools\Value\Question\ChecklistQuestion;
use Ibuildings\QaTools\Value\Question\MultipleChoiceQuestion;
use Ibuildings\QaTools\Value\Question\TextualQuestion;
use Ibuildings\QaTools\Value\Question\Question;
use Ibuildings\QaTools\Value\Question\YesOrNoQuestion;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question as ConsoleQuestion;

final class ConsoleQuestionFactory
{
    const MAX_ATTEMPTS = 3;

    /**
     * @var ConsoleQuestionFormatter
     */
    private $consoleQuestionFormatter;

    public function __construct(ConsoleQuestionFormatter $consoleQuestionFormatter)
    {
        $this->consoleQuestionFormatter = $consoleQuestionFormatter;
    }

    /**
     * @param Question $question
     * @return ChoiceQuestion|ConsoleQuestion
     */
    public function createFrom(Question $question)
    {
        switch (get_class($question)) {
            case TextualQuestion::class:
                /** @var TextualQuestion $question */
                return $this->createFromTextualQuestion($question);
                break;

            case YesOrNoQuestion::class:
                /** @var YesOrNoQuestion $question */
                return $this->createFromYesOrNoQuestion($question);
                break;

            case MultipleChoiceQuestion::class:
                /** @var MultipleChoiceQuestion $question */
                return $this->createFromMultipleChoiceQuestion($question);
                break;

            case ChecklistQuestion::class:
                /** @var ChecklistQuestion $question */
                return $this->createFromChecklistQuestion($question);
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

    /**
     * @param TextualQuestion $question
     * @return ConsoleQuestion
     */
    public function createFromTextualQuestion(TextualQuestion $question)
    {
        $consoleQuestion = new ConsoleQuestion(
            $this->consoleQuestionFormatter->formatTextualQuestion($question),
            $question->getDefaultAnswerValue()
        );

        $consoleQuestion->setValidator($this->answerIsPresentValidator());
        $consoleQuestion->setMaxAttempts(self::MAX_ATTEMPTS);

        return $consoleQuestion;
    }

    /**
     * @param YesOrNoQuestion $question
     * @return ConsoleQuestion
     */
    public function createFromYesOrNoQuestion(YesOrNoQuestion $question)
    {
        $consoleQuestion = new ConsoleQuestion(
            $this->consoleQuestionFormatter->formatYesOrNoQuestion($question),
            $question->getDefaultAnswerAsValue()
        );

        $consoleQuestion->setValidator(
            function ($answer) {
                if (is_bool($answer)) {
                    return $answer;
                }

                if (preg_match('/^(y|yes|n|no)$/i', $answer) === 0) {
                    throw new InvalidAnswerException(
                        'A yes or no question can only be answered with yes/y or no/n'
                    );
                }

                return strtolower(substr($answer, 0, 1)) === 'y';
            }
        );

        $consoleQuestion->setMaxAttempts(self::MAX_ATTEMPTS);

        return $consoleQuestion;
    }

    /**
     * @param MultipleChoiceQuestion $question
     * @return ChoiceQuestion
     */
    public function createFromMultipleChoiceQuestion(MultipleChoiceQuestion $question)
    {
        $consoleQuestion = new ChoiceQuestion(
            $this->consoleQuestionFormatter->formatMultipleChoiceQuestion($question),
            $question->getPossibleChoiceValues(),
            $question->getDefaultAnswerValue()
        );

        $consoleQuestion->setValidator($this->answerIsPresentValidator());

        return $consoleQuestion;
    }

    /**
     * @param ChecklistQuestion $question
     * @return ChoiceQuestion
     */
    public function createFromChecklistQuestion(ChecklistQuestion $question)
    {
        $consoleQuestion = new ChoiceQuestion(
            $this->consoleQuestionFormatter->formatChecklistQuestion($question),
            $question->getPossibleChoiceValues(),
            $question->getDefaultChoiceValues()
        );

        $consoleQuestion->setMultiselect(true);

        $consoleQuestion->setValidator($this->answerIsPresentValidator());

        return $consoleQuestion;
    }

    /**
     * @return \Closure
     */
    private function answerIsPresentValidator()
    {
        return function ($answer) {
            if ($answer === null) {
                throw new InvalidAnswerException('No answer given. Please provide an answer.');
            }

            return $answer;
        };
    }
}
