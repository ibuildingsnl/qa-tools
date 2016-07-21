<?php

namespace Ibuildings\QaTools\UnitTest\Core\IO\Cli\Validator;

use Ibuildings\QaTools\Core\Exception\InvalidAnswerGivenException;
use Ibuildings\QaTools\Core\Exception\InvalidArgumentException;
use Ibuildings\QaTools\Core\IO\Cli\Validator\YesOrNoAnswerValidator;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @group Interviewer
 * @group Conversation
 * @group Answer
 * @group Console
 * @group Validator
 */
class YesOrNoAnswerValidatorTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::all
     */
    public function validator_only_accepts_booleans_or_strings_as_answers($answer)
    {
        if (is_bool($answer) || is_string($answer)) {
            return;
        }

        $this->expectException(InvalidArgumentException::class);

        YesOrNoAnswerValidator::validate($answer);
    }

    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::boolean
     */
    public function validator_passes_through_boolean_answers($answer)
    {
        $validatedAnswer = YesOrNoAnswerValidator::validate($answer);

        $this->assertEquals($answer, $validatedAnswer);
    }

    /**
     * @test
     *
     * @dataProvider nonYesOrNoStringsProvider
     */
    public function validator_sees_string_answers_that_are_not_yes_y_no_or_n_as_invalid($answer)
    {
        $this->expectException(InvalidAnswerGivenException::class);

        YesOrNoAnswerValidator::validate($answer);
    }

    /**
     * @test
     *
     * @dataProvider caseInsensitiveYesProvider
     */
    public function validator_passes_true_for_string_answers_that_are_yes_or_y_regardless_of_case($answer)
    {
        $validatedAnswer = YesOrNoAnswerValidator::validate($answer);

        $this->assertTrue($validatedAnswer);
    }

    /**
     * @test
     *
     * @dataProvider caseInsensitiveNoProvider
     */
    public function validator_passes_false_for_string_answers_that_are_no_or_n_regardless_of_case($answer)
    {
        $validatedAnswer = YesOrNoAnswerValidator::validate($answer);

        $this->assertFalse($validatedAnswer);
    }

    public function nonYesOrNoStringsProvider()
    {
        return [
            ['ok'],
            ['ye'],
            [''],
            [' '],
            ['\n'],
            ['\t']
        ];
    }

    public function caseInsensitiveYesProvider()
    {
        return [
            ['YES'],
            ['yES'],
            ['YeS'],
            ['YEs'],
            ['yEs'],
            ['yeS'],
            ['Yes'],
            ['Y'],
            ['y'],
        ];
    }

    public function caseInsensitiveNoProvider()
    {
        return [
            ['NO'],
            ['No'],
            ['nO'],
            ['N'],
            ['n'],
        ];
    }
}
