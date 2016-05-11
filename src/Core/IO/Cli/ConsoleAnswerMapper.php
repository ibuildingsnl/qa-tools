<?php

namespace Ibuildings\QaTools\Core\IO\Cli;

use Ibuildings\QaTools\Value\Answer\MultipleAnswers;
use Ibuildings\QaTools\Value\Answer\SingleAnswer;
use Ibuildings\QaTools\Value\Answer\YesNoAnswer;

final class ConsoleAnswerMapper
{
    public static function mapToQaToolsAnswer($answer)
    {
        if ($answer === true) {
            return YesNoAnswer::yes();
        }

        if ($answer === false) {
            return YesNoAnswer::no();
        }

        if (is_array($answer)) {
            return new MultipleAnswers(array_map(
                function ($singleAnswer) {
                    return new SingleAnswer($singleAnswer);
                },
            $answer));
        }

        return new SingleAnswer($answer);
    }
}
