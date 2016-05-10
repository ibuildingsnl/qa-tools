<?php

use Ibuildings\QaTools\Assert\Assertion;
use Ibuildings\QaTools\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase as TestCase;

class AssertionTest extends TestCase
{
    /**
     * @test
     * @group Assertion
     */
    public function non_empty_strings_are_valid()
    {
        Assertion::nonEmptyString('0', 'test');
        Assertion::nonEmptyString('text', 'test');
        Assertion::nonEmptyString("new\nlines\nincluded", 'test');
    }

    /**
     * @test
     * @group Assertion
     *
     * @dataProvider \Ibuildings\QaTools\TestDataProvider::notStringOrEmptyString()
     *
     * @param mixed $value
     */
    public function non_strings_or_empty_strings_are_invalid($value)
    {
        $this->expectException(InvalidArgumentException::class);

        Assertion::nonEmptyString($value, 'value');
    }
}
