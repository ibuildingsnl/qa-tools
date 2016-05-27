<?php

namespace Ibuildings\QaTools\Core\IO\Cli;

use Ibuildings\QaTools\Core\Exception\InvalidAnswerGivenException;
use Ibuildings\QaTools\Core\Exception\InvalidArgumentException;
use Ibuildings\QaTools\Core\Interviewer\Question\ListChoiceQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\MultipleChoiceQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\TextualQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\Question;
use Ibuildings\QaTools\Core\Interviewer\Question\YesOrNoQuestion;
use Ibuildings\QaTools\Core\IO\Cli\Validator\TextualAnswerValidator;
use Ibuildings\QaTools\Core\IO\Cli\Validator\YesOrNoAnswerValidator;
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

            case ListChoiceQuestion::class:
                return $this->createFromListChoiceQuestion($question);
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
        $consoleQuestion->setValidator([TextualAnswerValidator::class, 'validate']);
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

        $consoleQuestion->setValidator([YesOrNoAnswerValidator::class, 'validate']);
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
            $question->getPossibleChoices()->convertToArrayOfStrings(),
            $question->getDefaultAnswer()->getAnswer()
        );

        $consoleQuestion->setMaxAttempts(self::MAX_ATTEMPTS);

        return $consoleQuestion;
    }

    /**
     * @param ListChoiceQuestion $question
     * @return ChoiceQuestion
     */
    public function createFromListChoiceQuestion(ListChoiceQuestion $question)
    {
        $consoleQuestion = new ChoiceQuestion(
            $this->consoleQuestionFormatter->formatListChoiceQuestion($question),
            $question->getPossibleChoices()->convertToArrayOfStrings(),
            $question->getDefaultAnswer()->convertToString()
        );

        $consoleQuestion->setMultiselect(true);
        $consoleQuestion->setMaxAttempts(self::MAX_ATTEMPTS);

        return $consoleQuestion;
    }
}
