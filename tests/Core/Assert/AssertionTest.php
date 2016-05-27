<?php

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @group Assertion
 */
class AssertionTest extends TestCase
{
    /**
     * @test
     */
    public function non_empty_strings_are_valid()
    {
        Assertion::nonEmptyString('0', 'test');
        Assertion::nonEmptyString('text', 'test');
        Assertion::nonEmptyString("new\nlines\nincluded", 'test');
    }

    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\TestDataProvider::notStringOrEmptyString()
     */
    public function non_strings_or_empty_strings_are_invalid($value)
    {
        $this->expectException(InvalidArgumentException::class);

        Assertion::nonEmptyString($value, 'value');
    }
}
