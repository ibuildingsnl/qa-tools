<?php

use Ibuildings\QaTools\Core\Interviewer\Answer\MissingAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
use Ibuildings\QaTools\Core\Interviewer\Question\TextualQuestion;
use PHPUnit_Framework_TestCase as TestCase;

class TextualQuestionTest extends TestCase
{
    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     *
     * @dataProvider \Ibuildings\QaTools\TestDataProvider::notString()
     */
    public function textual_questions_question_can_only_be_string($value)
    {
        $this->expectException(InvalidArgumentException::class);

        $defaultAnswer = new TextualAnswer('An answer.');

        new TextualQuestion($value, $defaultAnswer);
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function textual_questions_answer_defaults_to_missing_answer_if_none_given()
    {
        $expectedDefaultAnswer = new MissingAnswer;

        $question = new TextualQuestion('A question?');

        $this->assertEquals($expectedDefaultAnswer, $question->getDefaultAnswer());
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function textual_question_does_not_equal_another_textual_question_with_a_different_question()
    {
        $defaultAnswer = new TextualAnswer('An answer.');

        $question          = new TextualQuestion('A question?', $defaultAnswer);
        $differentQuestion = new TextualQuestion('A different question?', $defaultAnswer);

        $this->assertFalse($question->equals($differentQuestion));
    }


    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function textual_question_does_not_equal_another_textual_question_with_different_default_answer()
    {
        $defaultAnswer          = new TextualAnswer('An answer.');
        $differentDefaultAnswer = new TextualAnswer('A different answer.');

        $question          = new TextualQuestion('The question?', $defaultAnswer);
        $differentQuestion = new TextualQuestion('The question?', $differentDefaultAnswer);

        $this->assertFalse($question->equals($differentQuestion));
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function textual_question_equals_another_textual_question()
    {
        $defaultAnswer = new TextualAnswer('An answer.');

        $question     = new TextualQuestion('The question?', $defaultAnswer);
        $sameQuestion = new TextualQuestion('The question?', $defaultAnswer);

        $this->assertTrue($question->equals($sameQuestion));
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function textual_question_has_a_question_value()
    {
        $expectedQuestionValue = 'The question?';

        $question            = new TextualQuestion($expectedQuestionValue, new TextualAnswer('An answer.'));
        $actualQuestionValue = $question->getQuestion();

        $this->assertEquals($expectedQuestionValue, $actualQuestionValue);
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function textual_question_has_a_default_answer()
    {
        $expectedDefaultAnswer = new TextualAnswer('An answer.');

        $question            = new TextualQuestion('A question?', $expectedDefaultAnswer);
        $actualDefaultAnswer = $question->getDefaultAnswer();

        $this->assertEquals($expectedDefaultAnswer, $actualDefaultAnswer);
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function textual_question_can_suggest_a_given_compatible_answer_as_default_answer()
    {
        $expectedDefaultAnswer = new TextualAnswer('A new answer');

        $question = new TextualQuestion('A question?', new TextualAnswer('An answer'));
        $updatedQuestion = $question->withDefaultAnswer($expectedDefaultAnswer);

        $this->assertNotEquals($question, $updatedQuestion);
        $this->assertEquals($expectedDefaultAnswer, $updatedQuestion->getDefaultAnswer());
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function textual_question_is_converted_to_string_correctly()
    {
        $question = 'A question?';
        $expectedString = TextualQuestion::class . '(question="' . $question . '")';

        $actualQuestion = new TextualQuestion($question);

        $this->assertEquals($expectedString, (string) $actualQuestion);
    }
}
