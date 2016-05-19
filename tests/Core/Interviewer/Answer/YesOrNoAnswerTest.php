<?php

use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use PHPUnit_Framework_TestCase as TestCase;

class YesOrNoAnswerTest extends TestCase
{
    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Answer
     */
    public function yes_or_no_answer_does_not_equal_a_different_yes_or_no_answer()
    {
        $answer          = YesOrNoAnswer::no();
        $differentAnswer = YesOrNoAnswer::yes();

        $this->assertFalse($answer->equals($differentAnswer));
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Answer
     */
    public function yes_or_no_answer_equals_the_same_yes_or_no_answer()
    {
        $answer     = YesOrNoAnswer::yes();
        $sameAnswer = YesOrNoAnswer::yes();

        $this->assertTrue($answer->equals($sameAnswer));
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Answer
     */
    public function yes_or_no_answer_has_an_answer_value()
    {
        $answer = YesOrNoAnswer::yes();

        $actualValue = $answer->getAnswer();

        $this->assertTrue($actualValue);
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Answer
     */
    public function yes_or_no_answer_created_as_yes_is_yes()
    {
        $answer = YesOrNoAnswer::yes();

        $this->assertTrue($answer->isYes());
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Answer
     */
    public function yes_or_no_answer_created_as_no_is_no()
    {
        $answer = YesOrNoAnswer::no();

        $this->assertTrue($answer->isNo());
    }
}
