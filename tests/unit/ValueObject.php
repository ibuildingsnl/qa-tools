<?php

namespace Ibuildings\QaTools\UnitTest;

use Mockery;
use PHPUnit_Framework_Assert as Assert;

final class ValueObject
{
    /**
     * @param object $expected
     * @return Mockery\Matcher\Closure
     */
    public static function equals($expected)
    {
        return Mockery::on(function ($actual) use ($expected) {
            if (!$actual->equals($expected)) {
                Assert::assertEquals($expected, $actual, "Value object don't equal each other");
            }

            return true;
        });
    }
}
