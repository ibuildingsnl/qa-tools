<?php

use Ibuildings\QaTools\Value\Answer\SingleAnswer;
use Ibuildings\QaTools\Value\Answer\YesNoAnswer;
use Ibuildings\QaTools\Value\Question\OpenQuestion;
use Ibuildings\QaTools\Value\Question\YesNoQuestion;
use PHPUnit_Framework_TestCase as TestCase;

class YesNoQuestionTest extends TestCase
{
    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Question
     *
     * @dataProvider \Ibuildings\QaTools\TestDataProvider::notString()
     */
    public function question_can_only_be_string($value)
    {
        $this->expectException(InvalidArgumentException::class);

        $defaultAnswer = YesNoAnswer::no();

        new YesNoQuestion($value, $defaultAnswer);
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Question
     */
    public function questions_answer_defaults_to_yes_if_none_given()
    {
        $defaultAnswer = YesNoAnswer::yes();

        $question = new YesNoQuestion('A question?');
        $sameQuestion = new YesNoQuestion('A question?', $defaultAnswer);

        $this->assertTrue($question->equals($sameQuestion));
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Question
     */
    public function question_does_not_equal_another_question_with_a_different_question()
    {
        $defaultAnswer   = YesNoAnswer::yes();

        $question = new YesNoQuestion('A question?', $defaultAnswer);
        $differentQuestion = new YesNoQuestion('A different question?', $defaultAnswer);

        $this->assertFalse($question->equals($differentQuestion));
    }


    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Question
     */
    public function question_does_not_equal_another_question_with_different_default_answer()
    {
        $defaultAnswer   = YesNoAnswer::yes();
        $differentDefaultAnswer = YesNoAnswer::no();

        $question = new YesNoQuestion('The question?', $defaultAnswer);
        $differentQuestion = new YesNoQuestion('The question?', $differentDefaultAnswer);

        $this->assertFalse($question->equals($differentQuestion));
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Question
     */
    public function question_does_not_equal_a_question_of_a_different_type()
    {
        $question = new YesNoQuestion('The question?', YesNoAnswer::yes());
        $otherTypeOfQuestion = new OpenQuestion('The question?', new SingleAnswer('An answer.'));

        $this->assertFalse($question->equals($otherTypeOfQuestion));
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Question
     */
    public function question_equals_another_question()
    {
        $defaultAnswer = YesNoAnswer::yes();

        $question = new YesNoQuestion('The question?', $defaultAnswer);
        $sameQuestion = new YesNoQuestion('The question?', $defaultAnswer);

        $this->assertTrue($question->equals($sameQuestion));
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Question
     */
    public function question_has_a_question_value()
    {
        $expectedQuestionValue = 'The question?';

        $question = new YesNoQuestion($expectedQuestionValue, YesNoAnswer::yes());
        $actualQuestionValue = $question->getQuestion();

        $this->assertEquals($expectedQuestionValue, $actualQuestionValue);
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Question
     */
    public function question_has_a_default_answer()
    {
        $expectedDefaultAnswer = YesNoAnswer::yes();

        $question = new YesNoQuestion('The question?', $expectedDefaultAnswer);
        $actualDefaultAnswer = $question->getDefaultAnswer();

        $this->assertEquals($expectedDefaultAnswer, $actualDefaultAnswer);
    }
}
