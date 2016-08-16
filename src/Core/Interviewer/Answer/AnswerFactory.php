<?php

namespace Ibuildings\QaTools\Core\Interviewer\Answer;

final class AnswerFactory
{
    /**
     * @param boolean|string|string[] $answer
     * @return Choices|TextualAnswer|YesOrNoAnswer
     */
    public static function createFrom($answer)
    {
        if ($answer === true) {
            return YesOrNoAnswer::yes();
        }

        if ($answer === false) {
            return YesOrNoAnswer::no();
        }

        if (is_array($answer)) {
            return new Choices(
                array_map(
                    function ($singleAnswer) {
                        return new TextualAnswer($singleAnswer);
                    },
                    $answer
                )
            );
        }

        return new TextualAnswer($answer);
    }
}
