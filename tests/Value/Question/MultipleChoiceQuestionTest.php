<?php

use Ibuildings\QaTools\Value\Answer\MultipleAnswers;
use Ibuildings\QaTools\Value\Answer\SingleAnswer;
use Ibuildings\QaTools\Value\Question\MultipleChoiceQuestion;
use Ibuildings\QaTools\Value\Question\OpenQuestion;
use PHPUnit_Framework_TestCase as TestCase;

class MultipleChoiceQuestionTest extends TestCase
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

        $defaultAnswer   = new SingleAnswer('An answer.');
        $multipleAnswers = new MultipleAnswers([new SingleAnswer('An answer.')]);

        new MultipleChoiceQuestion($value, $multipleAnswers, $defaultAnswer);
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Question
     */
    public function question_cannot_have_a_default_value_that_is_not_a_possible_answer()
    {
        $this->expectException(LogicException::class);

        $possibleAnswers = new MultipleAnswers([new SingleAnswer('An answer')]);
        $defaultAnswer   = new SingleAnswer('A different answer');

        new MultipleChoiceQuestion('A question?', $possibleAnswers, $defaultAnswer);
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Question
     */
    public function question_does_not_equal_another_question_with_a_different_question()
    {
        $multipleAnswers = new MultipleAnswers([new SingleAnswer('An answer.')]);
        $defaultAnswer   = new SingleAnswer('An answer.');

        $question          = new MultipleChoiceQuestion('The question?', $multipleAnswers, $defaultAnswer);
        $differentQuestion = new MultipleChoiceQuestion('Another question?', $multipleAnswers, $defaultAnswer);

        $this->assertFalse($question->equals($differentQuestion));
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Question
     */
    public function question_does_not_equal_another_question_with_different_possible_answers()
    {
        $possibleAnswers = new MultipleAnswers(
            [new SingleAnswer('An answer.'), new SingleAnswer('A different answer.')]
        );

        $defaultAnswer          = new SingleAnswer('An answer.');
        $differentDefaultAnswer = new SingleAnswer('A different answer.');

        $question          = new MultipleChoiceQuestion('The question?', $possibleAnswers, $defaultAnswer);
        $differentQuestion = new MultipleChoiceQuestion('The question?', $possibleAnswers, $differentDefaultAnswer);

        $this->assertFalse($question->equals($differentQuestion));
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Question
     */
    public function question_does_not_equal_another_question_with_different_default_answers()
    {
        $possibleAnswers          = new MultipleAnswers([new SingleAnswer('An answer.')]);
        $differentPossibleAnswers = new MultipleAnswers(
            [
                new SingleAnswer('An answer.'),
                new SingleAnswer('A different answer.'),
            ]
        );

        $defaultAnswer = new SingleAnswer('An answer.');

        $question          = new MultipleChoiceQuestion('The question?', $possibleAnswers, $defaultAnswer);
        $differentQuestion = new MultipleChoiceQuestion('The question?', $differentPossibleAnswers, $defaultAnswer);

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
        $question            = new MultipleChoiceQuestion(
            'The question?',
            new MultipleAnswers([new SingleAnswer('An answer')]),
            new SingleAnswer('An answer')
        );
        $otherTypeOfQuestion = new OpenQuestion('The question?', new SingleAnswer('Default answer'));

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
        $multipleAnswers = new MultipleAnswers([new SingleAnswer('An answer.')]);
        $defaultAnswer   = new SingleAnswer('An answer.');

        $question = new MultipleChoiceQuestion('The question?', $multipleAnswers, $defaultAnswer);
        $same     = new MultipleChoiceQuestion('The question?', $multipleAnswers, $defaultAnswer);

        $this->assertTrue($question->equals($same));
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

        $multipleAnswers     = new MultipleAnswers([new SingleAnswer('An answer.')]);
        $question            = new MultipleChoiceQuestion(
            $expectedQuestionValue,
            $multipleAnswers,
            new SingleAnswer('An answer.')
        );
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

        $possibleAnswers     = new MultipleAnswers([
            new SingleAnswer('An answer.'), new SingleAnswer('Another answer')
        ]);
        $question            = new MultipleChoiceQuestion('A question?', $possibleAnswers, $expectedDefaultAnswer);
        $actualDefaultAnswer = $question->getDefaultAnswer();

        $this->assertEquals($expectedDefaultAnswer, $actualDefaultAnswer);
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Question
     */
    public function question_has_possible_answers()
    {
        $expectedPossibleAnswers = new MultipleAnswers(
            [new SingleAnswer('An answer.'), new SingleAnswer('Another answer')]
        );

        $defaultAnswer         = new SingleAnswer('An answer.');
        $question              = new MultipleChoiceQuestion('A question?', $expectedPossibleAnswers, $defaultAnswer);
        $actualPossibleAnswers = $question->getPossibleAnswers();

        $this->assertEquals($expectedPossibleAnswers, $actualPossibleAnswers);
    }
}
