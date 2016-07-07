<?php

use Ibuildings\QaTools\Core\Exception\InvalidArgumentException;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @group Conversation
 * @group Interviewer
 * @group Answer
 */
class TextualAnswerTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\TestDataProvider::notString()
     */
    public function textual_answer_can_only_be_string($value)
    {
        $this->expectException(InvalidArgumentException::class);

        new TextualAnswer($value);
    }

    /**
     * @test
     */
    public function textual_answer_does_not_equal_another_answer()
    {
        $answer      = new TextualAnswer('The answer.');
        $otherAnswer = new TextualAnswer('Another answer.');

        $this->assertFalse($answer->equals($otherAnswer));
    }

    /**
     * @test
     */
    public function textual_answer_equals_another_answer()
    {
        $answer      = new TextualAnswer('The answer.');
        $otherAnswer = new TextualAnswer('The answer.');

        $this->assertTrue($answer->equals($otherAnswer));
    }

    /**
     * @test
     */
    public function textual_answer_has_an_textual_answer_value()
    {
        $expectedValue = 'An answer.';
        $answer        = new TextualAnswer($expectedValue);

        $actualValue = $answer->getAnswer();

        $this->assertEquals($actualValue, $expectedValue);
    }
}
