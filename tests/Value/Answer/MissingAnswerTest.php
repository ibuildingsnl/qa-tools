<?php

use Ibuildings\QaTools\Value\Answer\MissingAnswer;
use Ibuildings\QaTools\Value\Answer\TextualAnswer;
use PHPUnit_Framework_TestCase as TestCase;

class NoAnswerTest extends TestCase
{
    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Answer
     */
    public function missing_answer_answer_has_a_null_answer_value()
    {
        $missingAnswer = new MissingAnswer();
        
        $this->assertNull($missingAnswer->getAnswer());
    }
    
    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Answer
     */
    public function missing_answer_does_not_equal_answer_of_other_type()
    {
        $missingAnswer = new MissingAnswer();
        $otherAnswer = new TextualAnswer('Test');
        
        $this->assertFalse($missingAnswer->equals($otherAnswer));
    }
    
    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Answer
     */
    public function missing_answer_equals_another_missing_answer()
    {
        $missingAnswer = new MissingAnswer();
        $sameAnswer = new MissingAnswer();
        
        $this->assertTrue($missingAnswer->equals($sameAnswer));
    }
}
