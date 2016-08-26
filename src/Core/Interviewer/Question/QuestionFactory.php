<?php

namespace Ibuildings\QaTools\Core\Interviewer\Question;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Interviewer\Answer\Choices;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;

final class QuestionFactory
{
    /**
     * @param string $question
     * @param null|string $defaultAnswer
     * @return TextualQuestion
     */
    public static function create($question, $defaultAnswer = null)
    {
        Assertion::nonEmptyString($question, 'Expected non-empty string for "%3$s", got "%s" of type "%s"', 'question');
        Assertion::nullOrNonEmptyString($defaultAnswer, 'default answer');

        if ($defaultAnswer === null) {
            return new TextualQuestion($question);
        }

        return new TextualQuestion($question, new TextualAnswer($defaultAnswer));
    }

    /**
     * @param string $question
     * @param bool|null $defaultAnswer When boolean, one of the `YesOrNoAnswer::*` constants.
     * @return YesOrNoQuestion
     */
    public static function createYesOrNo($question, $defaultAnswer = null)
    {
        Assertion::nonEmptyString($question, 'Expected non-empty string for "%3$s", got "%s" of type "%s"', 'question');
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
        Assertion::nonEmptyString($question, 'Expected non-empty string for "%3$s", got "%s" of type "%s"', 'question');
        Assertion::allNonEmptyString($choices, 'choices');
        Assertion::nullOrNonEmptyString($defaultAnswer, 'default answer');

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
        Assertion::nonEmptyString($question, 'Expected non-empty string for "%3$s", got "%s" of type "%s"', 'question');
        Assertion::allNonEmptyString($choices, 'choices');
        Assertion::nullOrallNonEmptyString($defaultAnswer, 'default answer');

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
