<?php

namespace Ibuildings\QaTools\UnitTest\Core\IO\Cli\Validator;

use Ibuildings\QaTools\Core\Exception\InvalidAnswerGivenException;
use Ibuildings\QaTools\Core\Exception\InvalidArgumentException;
use Ibuildings\QaTools\Core\IO\Cli\Validator\TextualAnswerValidator;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @group Interviewer
 * @group Conversation
 * @group Answer
 * @group Console
 * @group Validator
 */
class TextualAnswerValidatorTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notString
     */
    public function validator_only_accepts_null_or_string_as_answer($answer)
    {
        if ($answer === null) {
            return;
        }

        $this->expectException(InvalidArgumentException::class);

        TextualAnswerValidator::validate($answer);
    }

    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::emptyString
     */
    public function validator_sees_empty_or_emptyish_strings_as_invalid_answers($answer)
    {
        $this->expectException(InvalidAnswerGivenException::class);

        TextualAnswerValidator::validate($answer);
    }

    /**
     * @test
     */
    public function validator_sees_null_as_invalid_answers()
    {
        $this->expectException(InvalidAnswerGivenException::class);

        TextualAnswerValidator::validate(null);
    }

    /**
     * @test
     */
    public function validator_passes_through_answers_that_are_non_empty_strings()
    {
        $answer = 'An answer';

        $validatedAnswer = TextualAnswerValidator::validate($answer);

        $this->assertEquals($answer, $validatedAnswer);
    }
}
