<?php

use Ibuildings\QaTools\Core\Interviewer\Answer\Factory\AnswerFactory;
use Ibuildings\QaTools\Core\Interviewer\Answer\Choices;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use PHPUnit_Framework_TestCase as TestCase;

class AnswerFactoryTest extends TestCase
{
    /**
     * @test
     * @group Conversation
     * @group Console
     * @group Answer
     */
    public function factory_creates_a_positive_yes_or_no_answer()
    {
        $expectedAnswer = YesOrNoAnswer::yes();
        $actualAnswer   = AnswerFactory::createFrom(true);

        $this->assertEquals($expectedAnswer, $actualAnswer);
    }

    /**
     * @test
     * @group Conversation
     * @group Console
     * @group Answer
     */
    public function factory_creates_a_negative_yes_or_no_answer()
    {
        $expectedAnswer = YesOrNoAnswer::no();
        $actualAnswer   = AnswerFactory::createFrom(false);

        $this->assertEquals($expectedAnswer, $actualAnswer);
    }

    /**
     * @test
     * @group Conversation
     * @group Console
     * @group Answer
     */
    public function factory_creates_a_textual_answer()
    {
        $answerText     = 'Test answer.';
        $expectedAnswer = new TextualAnswer($answerText);
        $actualAnswer   = AnswerFactory::createFrom($answerText);

        $this->assertEquals($expectedAnswer, $actualAnswer);
    }

    /**
     * @test
     * @group Conversation
     * @group Console
     * @group Answer
     */
    public function factory_creates_choices()
    {
        $answerTexts    = ['Test answer.', 'Another test answer'];
        $expectedAnswer = new Choices(
            [
                new TextualAnswer($answerTexts[0]),
                new TextualAnswer($answerTexts[1]),
            ]
        );

        $actualAnswer = AnswerFactory::createFrom($answerTexts);

        $this->assertEquals($expectedAnswer, $actualAnswer);
    }
}
