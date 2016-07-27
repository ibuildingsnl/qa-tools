<?php

namespace Ibuildings\QaTools\UnitTest\Core\Interviewer\Answer;

use Ibuildings\QaTools\Core\Interviewer\Answer\NoDefaultAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @group Conversation
 * @group Interviewer
 * @group Answer
 */
class NoDefaultAnswerTest extends TestCase
{
    /**
     * @test
     */
    public function no_default_answer_answer_has_a_null_answer_value()
    {
        $noDefaultAnswer = new NoDefaultAnswer();
        
        $this->assertNull($noDefaultAnswer->getRaw());
    }
    
    /**
     * @test
     */
    public function no_default_answer_does_not_equal_answer_of_other_type()
    {
        $noDefaultAnswer = new NoDefaultAnswer();
        $otherAnswer = new TextualAnswer('Test');

        $this->assertFalse($noDefaultAnswer->equals($otherAnswer));
    }

    /**
     * @test
     */
    public function no_default_answer_equals_another_missing_answer()
    {
        $noDefaultAnswer = new NoDefaultAnswer();
        $sameAnswer = new NoDefaultAnswer();

        $this->assertTrue($noDefaultAnswer->equals($sameAnswer));
    }

    /**
     * @test
     */
    public function no_default_answer_is_converted_to_an_empty_string()
    {
        $noDefaultAnswer = new NoDefaultAnswer();

        $this->assertEquals('', $noDefaultAnswer->convertToString());
    }
}
