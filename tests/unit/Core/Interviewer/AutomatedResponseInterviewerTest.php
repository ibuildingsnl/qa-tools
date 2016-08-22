<?php

namespace Ibuildings\QaTools\UnitTest\Core\Interviewer;

use Ibuildings\QaTools\Core\Exception\RuntimeException;
use Ibuildings\QaTools\Core\Interviewer\Answer\AnswerFactory;
use Ibuildings\QaTools\Core\Interviewer\AutomatedResponseInterviewer;
use Ibuildings\QaTools\Core\Interviewer\Question\QuestionFactory;
use Ibuildings\QaTools\UnitTest\Diffing;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * @group Interviewer
 */
class AutomatedResponseInterviewerTest extends TestCase
{
    use Diffing;

    /** @test */
    public function answers_questions()
    {
        $questionText = "What's in a name?";
        $expectedAnswer = AnswerFactory::createFrom('Character');

        $interviewer = new AutomatedResponseInterviewer();
        $interviewer->recordAnswer($questionText, $expectedAnswer);

        $actualAnswer = $interviewer->ask(QuestionFactory::create($questionText));
        $this->assertTrue(
            $expectedAnswer->equals($actualAnswer),
            $this->diff($expectedAnswer, $actualAnswer, 'Given answer is different than the recorded answer')
        );
    }

    /** @test */
    public function fails_to_answer_when_theres_no_recorded_answer_for_a_question()
    {
        $interviewer = new AutomatedResponseInterviewer();
        $interviewer->recordAnswer("What's in a house?", AnswerFactory::createFrom('Character'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageRegExp("~No answer recorded for question.+What's in a name~");
        $interviewer->ask(QuestionFactory::create("What's in a name?"));
    }

    /** @test */
    public function accepts_default_answers()
    {
        $questionText = "What's in a name?";
        $expectedAnswer = AnswerFactory::createFrom('Character');

        $interviewer = new AutomatedResponseInterviewer();
        $interviewer->respondWithDefaultAnswerTo($questionText);

        $actualAnswer = $interviewer->ask(QuestionFactory::create($questionText, 'Character'));
        $this->assertTrue(
            $expectedAnswer->equals($actualAnswer),
            $this->diff($expectedAnswer, $actualAnswer, 'Given answer is different than the default answer')
        );
    }

    /** @test */
    public function fails_to_answer_when_theres_no_default_answer_to_accept()
    {
        $questionText = "What's in a name?";

        $interviewer = new AutomatedResponseInterviewer();
        $interviewer->respondWithDefaultAnswerTo($questionText);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageRegExp("~No default answer available for question.+What's in a name~");
        $interviewer->ask(QuestionFactory::create($questionText));
    }

    /** @test */
    public function gives_the_recorded_answer_before_given_the_default_answer()
    {
        $questionText = "What's in a name?";
        $recordedAnswer = AnswerFactory::createFrom('Character');
        $defaultAnswer = 'Default';

        $interviewer = new AutomatedResponseInterviewer();
        $interviewer->recordAnswer($questionText, $recordedAnswer);
        $interviewer->respondWithDefaultAnswerTo($questionText);

        $actualAnswer = $interviewer->ask(QuestionFactory::create($questionText, $defaultAnswer));
        $this->assertTrue(
            $recordedAnswer->equals($actualAnswer),
            $this->diff($recordedAnswer, $actualAnswer, 'Given answer is different than the recorded answer')
        );
    }
}
