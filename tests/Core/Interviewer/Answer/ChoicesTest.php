<?php

use Ibuildings\QaTools\Core\Interviewer\Answer\Choices;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
use PHPUnit_Framework_TestCase as TestCase;

class ChoicesTest extends TestCase
{
    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Answer
     *
     * @dataProvider \Ibuildings\QaTools\TestDataProvider::all()
     */
    public function choices_can_only_be_an_array_of_answers($value)
    {
        $this->expectException(InvalidArgumentException::class);

        new Choices([$value]);
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Answer
     */
    public function choices_do_not_contain_a_given_answer()
    {
        $answerToBeFound = new TextualAnswer('An answer.');

        $answers = new Choices([]);

        $this->assertFalse($answers->contain($answerToBeFound));
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Answer
     */
    public function choices_contain_a_given_answer()
    {
        $answerToBeFound = new TextualAnswer('An answer.');

        $answers = new Choices([$answerToBeFound]);

        $this->assertTrue($answers->contain($answerToBeFound));
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Answer
     */
    public function choices_do_not_equal_other_choices_if_different_length()
    {
        $answer = new TextualAnswer('Answer A');

        $choices          = new Choices([$answer]);
        $differentChoices = new Choices([]);

        $this->assertFalse($choices->equal($differentChoices));
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Answer
     */
    public function choices_do_not_equal_other_choices_with_different_values()
    {
        $answer          = new TextualAnswer('Answer A');
        $differentAnswer = new TextualAnswer('Answer B');

        $choices          = new Choices([$answer]);
        $differentChoices = new Choices([$differentAnswer]);

        $this->assertFalse($choices->equal($differentChoices));
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Answer
     */
    public function choices_equal_other_choices()
    {
        $answer     = new TextualAnswer('Answer A');
        $sameAnswer = new TextualAnswer('Answer A');

        $choices     = new Choices([$answer]);
        $sameChoices = new Choices([$sameAnswer]);

        $this->assertTrue($choices->equal($sameChoices));
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Answer
     */
    public function choices_consist_of_multiple_answers()
    {
        $innerAnswers = [new TextualAnswer('An answer.'), new TextualAnswer('Another answer.')];
        $choices      = new Choices($innerAnswers);

        $actualValue = $choices->getAnswers();

        $this->assertEquals($innerAnswers, $actualValue);
    }
}
