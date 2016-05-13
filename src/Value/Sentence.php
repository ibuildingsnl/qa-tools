<?php

namespace Ibuildings\QaTools\Value;

use Ibuildings\QaTools\Assert\Assertion;

final class Sentence
{
    /**
     * @var string
     */
    private $sentence;

    public function __construct($sentence)
    {
        Assertion::string($sentence);
        $this->sentence = $sentence;
    }

    public function equals(Sentence $other)
    {
        return $this->sentence === $other->sentence;
    }

    /**
     * @return string
     */
    public function getSentence()
    {
        return $this->sentence;
    }
}
