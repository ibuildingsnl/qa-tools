<?php

namespace Ibuildings\QaTools\Core\Interviewer\Answer\Factory;

use Ibuildings\QaTools\Core\Interviewer\Answer\Choices;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;

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
