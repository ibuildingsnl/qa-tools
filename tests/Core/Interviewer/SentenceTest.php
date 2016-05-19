<?php

use Ibuildings\QaTools\Core\Interviewer\Sentence;
use PHPUnit_Framework_TestCase as TestCase;

class SentenceTest extends TestCase
{
    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Sentence
     *
     * @dataProvider \Ibuildings\QaTools\TestDataProvider::notString()
     */
    public function sentence_can_only_be_a_string($value)
    {
        $this->expectException(\Ibuildings\QaTools\Core\Exception\InvalidArgumentException::class);

        new Sentence($value);
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Sentence
     */
    public function sentence_does_not_equal_a_different_sentence()
    {
        $sentence = new Sentence('Hello');
        $differentSentence = new Sentence('Bye');

        $this->assertFalse($sentence->equals($differentSentence));
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Sentence
     */
    public function sentence_equals_the_same_sentence()
    {
        $sentence = new Sentence('Hello');
        $sameSentence = new Sentence('Hello');

        $this->assertTrue($sentence->equals($sameSentence));
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Sentence
     */
    public function sentence_has_a_given_value()
    {
        $sentenceValue = 'Hello';

        $sentence = new Sentence($sentenceValue);

        $this->assertEquals($sentenceValue, $sentence->getSentence());
    }
}
