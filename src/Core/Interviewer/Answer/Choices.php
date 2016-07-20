<?php

namespace Ibuildings\QaTools\Core\Interviewer\Answer;

use ArrayIterator;
use Countable;
use Ibuildings\QaTools\Core\Assert\Assertion;
use IteratorAggregate;

final class Choices implements Answer, IteratorAggregate, Countable
{
    /**
     * @var TextualAnswer[]
     */
    private $answers;

    /**
     * @param TextualAnswer[] $answers
     */
    public function __construct(array $answers)
    {
        Assertion::allIsInstanceOf($answers, TextualAnswer::class);
        $this->answers = $answers;
    }

    /**
     * @param TextualAnswer $other
     * @return bool
     */
    public function contain(TextualAnswer $other)
    {
        /** @var TextualAnswer $answer */
        foreach ($this->answers as $answer) {
            if ($answer->equals($other)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Choices $other
     * @return bool
     */
    public function equal(Choices $other)
    {
        if (count($other) !== count($this)) {
            return false;
        }

        foreach ($other as $otherAnswer) {
            if (!$this->contain($otherAnswer)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return TextualAnswer[]
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->answers);
    }

    public function count()
    {
        return count($this->answers);
    }

    public function convertToString()
    {
        return implode(', ', $this->convertToArrayOfStrings());
    }

    public function convertToArrayOfStrings()
    {
        return array_map(function (TextualAnswer $answer) {
            return $answer->getAnswer();
        }, $this->answers);
    }
}
