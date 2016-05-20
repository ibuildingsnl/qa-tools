<?php

namespace Ibuildings\QaTools\Core\IO\Cli;

use Ibuildings\QaTools\Core\Exception\InvalidAnswerGivenException;
use Ibuildings\QaTools\Core\Exception\InvalidArgumentException;
use Ibuildings\QaTools\Core\Interviewer\Question\ChecklistQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\MultipleChoiceQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\TextualQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\Question;
use Ibuildings\QaTools\Core\Interviewer\Question\YesOrNoQuestion;
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
                return $this->createFromTextualQuestion($question);
                break;

            case YesOrNoQuestion::class:
                return $this->createFromYesOrNoQuestion($question);
                break;

            case MultipleChoiceQuestion::class:
                return $this->createFromMultipleChoiceQuestion($question);
                break;

            case ChecklistQuestion::class:
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
            $question->getDefaultAnswer()->getAnswer()
        );

        $consoleQuestion->setValidator(function ($answer) {
            if ($answer === null || trim($answer) === '') {
                throw new InvalidAnswerGivenException('No answer given. Please provide an answer.');
            }

            return $answer;
        });
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
            $question->getDefaultAnswer()->getAnswer()
        );

        $consoleQuestion->setValidator(
            function ($answer) {
                if (preg_match('/^(y|yes|n|no)$/i', $answer) === 0) {
                    throw new InvalidAnswerGivenException(
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
            $question->getPossibleChoices()->convertToArray(),
            $question->getDefaultAnswer()->getAnswer()
        );

        $consoleQuestion->setMaxAttempts(self::MAX_ATTEMPTS);

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
            $question->getPossibleChoices()->convertToArray(),
            $question->getDefaultAnswer()->convertToString()
        );

        $consoleQuestion->setMultiselect(true);
        $consoleQuestion->setMaxAttempts(self::MAX_ATTEMPTS);

        return $consoleQuestion;
    }
}
