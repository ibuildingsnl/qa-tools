<?php

namespace Ibuildings\QaTools\Core\IO\Cli\Validator;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Exception\InvalidAnswerGivenException;

final class YesOrNoAnswerValidator
{
    public static function validate($answer)
    {
        // A default answer can only be a boolean: if that is the case, pass it through
        if (is_bool($answer)) {
            return $answer;
        }

        Assertion::string($answer);

        if (preg_match('/^(y|yes|n|no)$/i', $answer) === 0) {
            throw new InvalidAnswerGivenException(
                'A yes or no question can only be answered with yes/y or no/n'
            );
        }

        return strtolower(substr($answer, 0, 1)) === 'y';
    }
}
