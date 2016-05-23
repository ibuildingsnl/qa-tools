<?php

use Ibuildings\QaTools\Core\Interviewer\Answer\NoDefaultAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
use PHPUnit_Framework_TestCase as TestCase;

class NoDefaultAnswerTest extends TestCase
{
    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Answer
     */
    public function no_default_answer_answer_has_a_null_answer_value()
    {
        $missingAnswer = new NoDefaultAnswer();
        
        $this->assertNull($missingAnswer->getAnswer());
    }
    
    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Answer
     */
    public function no_default_answer_does_not_equal_answer_of_other_type()
    {
        $missingAnswer = new NoDefaultAnswer();
        $otherAnswer = new TextualAnswer('Test');

        $this->assertFalse($missingAnswer->equals($otherAnswer));
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Answer
     */
    public function no_default_answer_equals_another_missing_answer()
    {
        $missingAnswer = new NoDefaultAnswer();
        $sameAnswer = new NoDefaultAnswer();

        $this->assertTrue($missingAnswer->equals($sameAnswer));
    }
}
