<?php

namespace Ibuildings\QaTools\UnitTest\Core\Assert;

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
        Assertion::nonEmptyString('0', 'Expected non-empty string for "%3$s", got "%s" of type "%s"', 'test');
        Assertion::nonEmptyString('text', 'Expected non-empty string for "%3$s", got "%s" of type "%s"', 'test');
        Assertion::nonEmptyString("new\nlines\nincluded", 'Expected non-empty string for "%3$s", got "%s" of type "%s"', 'test');
    }

    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notStringOrEmptyString()
     */
    public function non_strings_or_empty_strings_are_invalid($value)
    {
        $this->expectException(InvalidArgumentException::class);

        Assertion::nonEmptyString($value, 'Expected non-empty string for "%3$s", got "%s" of type "%s"', 'value');
    }
}
