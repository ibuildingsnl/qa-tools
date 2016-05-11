<?php

use Ibuildings\QaTools\Value\Answer\MultipleAnswers;
use Ibuildings\QaTools\Value\Answer\SingleAnswer;
use Ibuildings\QaTools\Value\Question\MultipleChoiceQuestion;
use Ibuildings\QaTools\Value\Question\OpenQuestion;
use PHPUnit_Framework_TestCase as TestCase;

class OpenQuestionTest extends TestCase
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

        $defaultAnswer = new SingleAnswer('An answer.');

        new OpenQuestion($value, $defaultAnswer);
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Question
     */
    public function question_does_not_equal_another_question_with_a_different_question()
    {
        $defaultAnswer = new SingleAnswer('An answer.');

        $question          = new OpenQuestion('A question?', $defaultAnswer);
        $differentQuestion = new OpenQuestion('A different question?', $defaultAnswer);

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
        $defaultAnswer          = new SingleAnswer('An answer.');
        $differentDefaultAnswer = new SingleAnswer('A different answer.');

        $question          = new OpenQuestion('The question?', $defaultAnswer);
        $differentQuestion = new OpenQuestion('The question?', $differentDefaultAnswer);

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
        $defaultAnswer       = new SingleAnswer('Default answer');
        $question            = new OpenQuestion('The question?', $defaultAnswer);
        $otherTypeOfQuestion = new MultipleChoiceQuestion(
            'The question?',
            new MultipleAnswers([$defaultAnswer]),
            $defaultAnswer
        );

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
        $defaultAnswer = new SingleAnswer('An answer.');

        $question     = new OpenQuestion('The question?', $defaultAnswer);
        $sameQuestion = new OpenQuestion('The question?', $defaultAnswer);

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

        $question = new OpenQuestion($expectedQuestionValue, new SingleAnswer('An answer.'));
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
        $expectedDefaultAnswer = new SingleAnswer('An answer.');

        $question            = new OpenQuestion('A question?', $expectedDefaultAnswer);
        $actualDefaultAnswer = $question->getDefaultAnswer();

        $this->assertEquals($expectedDefaultAnswer, $actualDefaultAnswer);
    }
}
