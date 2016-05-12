<?php

namespace Ibuildings\QaTools\Value\Answer\Factory;

use Ibuildings\QaTools\Value\Answer\Choices;
use Ibuildings\QaTools\Value\Answer\TextualAnswer;
use Ibuildings\QaTools\Value\Answer\YesOrNoAnswer;

final class AnswerFactory
{
    /**
     * @param bool|string|string[] $answer
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
            return new Choices(array_map(
                function ($singleAnswer) {
                    return new TextualAnswer($singleAnswer);
                },
            $answer));
        }

        return new TextualAnswer($answer);
    }
}
