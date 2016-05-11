<?php

use Ibuildings\QaTools\Value\Answer\MultipleAnswers;
use Ibuildings\QaTools\Value\Answer\SingleAnswer;
use PHPUnit_Framework_TestCase as TestCase;

class MultipleAnswersTest extends TestCase
{
    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Answer
     *
     * @dataProvider \Ibuildings\QaTools\TestDataProvider::all()
     */
    public function answer_can_only_be_an_array_of_answers($value)
    {
        $this->expectException(InvalidArgumentException::class);

        new MultipleAnswers([$value]);
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Answer
     */
    public function answers_do_not_contain_a_given_answer()
    {
        $answerToBeFound = new SingleAnswer('An answer.');

        $answers = new MultipleAnswers([]);

        $this->assertFalse($answers->contains($answerToBeFound));
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Answer
     */
    public function answers_contain_a_given_answer()
    {
        $answerToBeFound = new SingleAnswer('An answer.');

        $answers = new MultipleAnswers([$answerToBeFound]);

        $this->assertTrue($answers->contains($answerToBeFound));
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Answer
     */
    public function answers_do_not_equal_other_answers_if_different_length()
    {
        $answer = new SingleAnswer('Answer A');

        $multipleAnswers = new MultipleAnswers([$answer]);
        $differentMultipleAnswers = new MultipleAnswers([]);

        $this->assertFalse($multipleAnswers->equals($differentMultipleAnswers));
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Answer
     */
    public function answers_do_not_equal_other_answers_with_different_values()
    {
        $answer = new SingleAnswer('Answer A');
        $differentAnswer = new SingleAnswer('Answer B');

        $multipleAnswers = new MultipleAnswers([$answer]);
        $differentMultipleAnswers = new MultipleAnswers([$differentAnswer]);

        $this->assertFalse($multipleAnswers->equals($differentMultipleAnswers));
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Answer
     */
    public function answer_does_not_equal_an_answer_of_a_different_type()
    {
        $multipleAnswers = new MultipleAnswers([new SingleAnswer('An answer.')]);
        $otherTypeOfAnswer = new SingleAnswer('An answer.');

        $this->assertFalse($multipleAnswers->equals($otherTypeOfAnswer));
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Answer
     */
    public function answers_equal_other_answers()
    {
        $answer = new SingleAnswer('Answer A');
        $sameAnswer = new SingleAnswer('Answer A');

        $multipleAnswers = new MultipleAnswers([$answer]);
        $sameMultipleAnswers = new MultipleAnswers([$sameAnswer]);

        $this->assertTrue($multipleAnswers->equals($sameMultipleAnswers));
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Answer
     */
    public function answer_has_multiple_answer_values()
    {
        $innerAnswers = [new SingleAnswer('An answer.'), new SingleAnswer('Another answer.')];
        $answer = new MultipleAnswers($innerAnswers);

        $actualValue = $answer->getAnswer();

        $this->assertEquals($innerAnswers, $actualValue);
    }
}
