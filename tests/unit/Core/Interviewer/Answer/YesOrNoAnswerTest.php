<?php

namespace Ibuildings\QaTools\UnitTest\Core\Interviewer\Answer;

use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Test\MockeryTestCase;

/**
 * @group Conversation
 * @group Interviewer
 * @group Answer
 */
class YesOrNoAnswerTest extends MockeryTestCase
{
    /**
     * @test
     */
    public function yes_or_no_answer_does_not_equal_a_different_yes_or_no_answer()
    {
        $answer          = YesOrNoAnswer::no();
        $differentAnswer = YesOrNoAnswer::yes();

        $this->assertFalse($answer->equals($differentAnswer));
    }

    /**
     * @test
     */
    public function yes_or_no_answer_equals_the_same_yes_or_no_answer()
    {
        $answer     = YesOrNoAnswer::yes();
        $sameAnswer = YesOrNoAnswer::yes();

        $this->assertTrue($answer->equals($sameAnswer));
    }

    /**
     * @test
     */
    public function yes_or_no_answer_has_an_answer_value()
    {
        $answer = YesOrNoAnswer::yes();

        $actualValue = $answer->getRaw();

        $this->assertTrue($actualValue);
    }

    /**
     * @test
     */
    public function yes_or_no_answer_created_as_yes_is_yes()
    {
        $answer = YesOrNoAnswer::yes();

        $this->assertTrue($answer->is(YesOrNoAnswer::YES));
    }

    /**
     * @test
     */
    public function yes_or_no_answer_created_as_no_is_no()
    {
        $answer = YesOrNoAnswer::no();

        $this->assertTrue($answer->is(YesOrNoAnswer::NO));
    }
}
