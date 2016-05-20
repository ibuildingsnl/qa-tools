<?php

use Ibuildings\QaTools\Core\Exception\InvalidArgumentException;
use Ibuildings\QaTools\Core\Interviewer\Answer\MissingAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\Question\YesOrNoQuestion;
use PHPUnit_Framework_TestCase as TestCase;

class YesOrNoQuestionTest extends TestCase
{
    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     *
     * @dataProvider \Ibuildings\QaTools\TestDataProvider::notString()
     */
    public function yes_or_no_questions_question_can_only_be_string($value)
    {
        $this->expectException(InvalidArgumentException::class);

        $defaultAnswer = YesOrNoAnswer::no();

        new YesOrNoQuestion($value, $defaultAnswer);
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function yes_or_no_questions_answer_defaults_to_no_answer_if_none_given()
    {
        $expectedDefaultAnswer = new MissingAnswer;

        $question = new YesOrNoQuestion('A question?');

        $this->assertEquals($expectedDefaultAnswer, $question->getDefaultAnswer());
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function yes_or_no_question_does_not_equal_another_yes_or_no_question_with_a_different_question()
    {
        $defaultAnswer = YesOrNoAnswer::yes();

        $question          = new YesOrNoQuestion('A question?', $defaultAnswer);
        $differentQuestion = new YesOrNoQuestion('A different question?', $defaultAnswer);

        $this->assertFalse($question->equals($differentQuestion));
    }


    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function yes_or_no_question_does_not_equal_another_yes_or_no_question_with_different_default_answer()
    {
        $defaultAnswer          = YesOrNoAnswer::yes();
        $differentDefaultAnswer = YesOrNoAnswer::no();

        $question          = new YesOrNoQuestion('The question?', $defaultAnswer);
        $differentQuestion = new YesOrNoQuestion('The question?', $differentDefaultAnswer);

        $this->assertFalse($question->equals($differentQuestion));
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function yes_or_no_question_equals_another_yes_or_no_question()
    {
        $defaultAnswer = YesOrNoAnswer::yes();

        $question     = new YesOrNoQuestion('The question?', $defaultAnswer);
        $sameQuestion = new YesOrNoQuestion('The question?', $defaultAnswer);

        $this->assertTrue($question->equals($sameQuestion));
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function yes_or_no_question_has_a_question_value()
    {
        $expectedQuestionValue = 'The question?';

        $question            = new YesOrNoQuestion($expectedQuestionValue, YesOrNoAnswer::yes());
        $actualQuestionValue = $question->getQuestion();

        $this->assertEquals($expectedQuestionValue, $actualQuestionValue);
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function yes_or_no_question_has_the_same_default_answer_as_given()
    {
        $expectedDefaultAnswer = YesOrNoAnswer::yes();

        $question            = new YesOrNoQuestion('The question?', $expectedDefaultAnswer);
        $actualDefaultAnswer = $question->getDefaultAnswer();

        $this->assertEquals($expectedDefaultAnswer, $actualDefaultAnswer);
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function yes_or_no_question_has_no_default_answer_if_none_given()
    {
        $question = new YesOrNoQuestion('The question?');

        $this->assertFalse($question->hasDefaultAnswer());
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function yes_or_no_question_has_a_default_answer_if_given()
    {
        $question = new YesOrNoQuestion('The question?', YesOrNoAnswer::yes());

        $this->assertTrue($question->hasDefaultAnswer());
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function yes_or_no_question_can_suggest_a_given_compatible_answer_as_default_answer()
    {
        $expectedDefaultAnswer = YesOrNoAnswer::yes();

        $question = new YesOrNoQuestion('A question?', YesOrNoAnswer::no());
        $updatedQuestion = $question->withDefaultAnswer($expectedDefaultAnswer);

        $this->assertNotEquals($question, $updatedQuestion);
        $this->assertEquals($expectedDefaultAnswer, $updatedQuestion->getDefaultAnswer());
    }
}
