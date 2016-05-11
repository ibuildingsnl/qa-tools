<?php

use Ibuildings\QaTools\Exception\InvalidArgumentException;
use Ibuildings\QaTools\Value\Answer\SingleAnswer;
use Ibuildings\QaTools\Value\Answer\YesNoAnswer;
use PHPUnit_Framework_TestCase as TestCase;

class SingleAnswerTest extends TestCase
{
    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Answer
     *
     * @dataProvider \Ibuildings\QaTools\TestDataProvider::notString()
     */
    public function answer_can_only_be_string($value)
    {
        $this->expectException(InvalidArgumentException::class);

        new SingleAnswer($value);
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Answer
     */
    public function answer_does_not_equal_another_answer()
    {
        $answer = new SingleAnswer('The answer.');
        $otherAnswer = new SingleAnswer('Another answer.');

        $this->assertFalse($answer->equals($otherAnswer));
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Answer
     */
    public function answer_does_not_equal_an_answer_of_a_different_type()
    {
        $answer = new SingleAnswer('false');

        $otherTypeOfAnswer = YesNoAnswer::no();

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
        $answer = new SingleAnswer('The answer.');
        $otherAnswer = new SingleAnswer('The answer.');

        $this->assertTrue($answer->equals($otherAnswer));
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Answer
     */
    public function answer_has_an_answer_value()
    {
        $expectedValue = 'An answer.';
        $answer = new SingleAnswer($expectedValue);

        $actualValue = $answer->getAnswer();

        $this->assertEquals($actualValue, $expectedValue);
    }
}
