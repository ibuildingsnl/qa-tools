<?php

namespace Ibuildings\QaTools\Core\IO\Cli\Validator;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Exception\InvalidAnswerGivenException;

final class TextualAnswerValidator
{
    public static function validate($answer)
    {
        Assertion::nullOrString($answer);

        if ($answer === null || trim($answer) === '') {
            throw new InvalidAnswerGivenException('No answer given. Please provide an answer.');
        }

        return $answer;
    }
}
