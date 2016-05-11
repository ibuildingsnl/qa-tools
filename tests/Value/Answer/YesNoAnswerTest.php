<?php

use Ibuildings\QaTools\Value\Answer\SingleAnswer;
use Ibuildings\QaTools\Value\Answer\YesNoAnswer;
use PHPUnit_Framework_TestCase as TestCase;

class YesNoAnswerTest extends TestCase
{
    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Answer
     */
    public function answer_does_not_equal_another_answer()
    {
        $answer = YesNoAnswer::no();
        $differentAnswer = YesNoAnswer::yes();

        $this->assertFalse($answer->equals($differentAnswer));
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Answer
     */
    public function answer_does_not_equal_an_answer_of_another_type()
    {
        $answer = YesNoAnswer::no();
        $otherTypeOfAnswer = new SingleAnswer('false');

        $this->assertFalse($answer->equals($otherTypeOfAnswer));
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Answer
     */
    public function answer_equals_another_answer()
    {
        $answer = YesNoAnswer::yes();
        $sameAnswer = YesNoAnswer::yes();

        $this->assertTrue($answer->equals($sameAnswer));
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Answer
     */
    public function answer_has_an_answer_value()
    {
        $answer = YesNoAnswer::yes();

        $actualValue = $answer->getAnswer();

        $this->assertEquals(true, $actualValue);
    }
}
