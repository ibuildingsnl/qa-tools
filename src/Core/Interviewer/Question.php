<?php

namespace Ibuildings\QaTools\Core\Interviewer;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Interviewer\Answer\Choices;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\Question\ListChoiceQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\MultipleChoiceQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\TextualQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\YesOrNoQuestion;

final class Question
{
    /**
     * @param string $question
     * @param null|string $defaultAnswer
     * @return TextualQuestion
     */
    public static function create($question, $defaultAnswer = null)
    {
        Assertion::string($question);
        Assertion::nullOrString($defaultAnswer);

        if ($defaultAnswer === null) {
            return new TextualQuestion($question);
        }

        return new TextualQuestion($question, new TextualAnswer($defaultAnswer));
    }

    /**
     * @param string $question
     * @param null|string $defaultAnswer
     * @return YesOrNoQuestion
     */
    public static function createYesOrNo($question, $defaultAnswer = null)
    {
        Assertion::string($question);
        Assertion::nullOrBoolean($defaultAnswer);

        if ($defaultAnswer === null) {
            return new YesOrNoQuestion($question);
        }

        if ($defaultAnswer === YesOrNoAnswer::YES) {
            return new YesOrNoQuestion($question, YesOrNoAnswer::yes());
        }

        return new YesOrNoQuestion($question, YesOrNoAnswer::no());
    }

    /**
     * @param string $question
     * @param array $choices
     * @param null|string $defaultAnswer
     * @return MultipleChoiceQuestion
     */
    public static function createMultipleChoice($question, array $choices, $defaultAnswer = null)
    {
        Assertion::string($question);
        Assertion::allString($choices);
        Assertion::nullOrString($defaultAnswer);

        $choicesWithAnswers = new Choices(array_map(function ($choice) {
            return new TextualAnswer($choice);
        }, $choices));

        if ($defaultAnswer === null) {
            return new MultipleChoiceQuestion($question, $choicesWithAnswers);
        }

        return new MultipleChoiceQuestion($question, $choicesWithAnswers, new TextualAnswer($defaultAnswer));
    }

    /**
     * @param string $question
     * @param string[] $choices
     * @param string[]|null $defaultAnswer
     * @return ListChoiceQuestion
     */
    public static function createListChoice($question, array $choices, array $defaultAnswer = null)
    {
        Assertion::string($question);
        Assertion::allString($choices);

        $choicesWithAnswers = new Choices(array_map(function ($choice) {
            return new TextualAnswer($choice);
        }, $choices));

        if ($defaultAnswer === null) {
            return new ListChoiceQuestion($question, $choicesWithAnswers);
        }

        $defaultAnswerChoices = new Choices(array_map(function ($answer) {
            return new TextualAnswer($answer);
        }, $defaultAnswer));

        return new ListChoiceQuestion($question, $choicesWithAnswers, $defaultAnswerChoices);
    }
}
