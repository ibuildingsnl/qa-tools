<?php

namespace Ibuildings\QaTools;

use stdClass;

final class TestDataProvider
{
    public static function all()
    {
        return array_merge(
            self::emptyString(),
            [
                'integer' => [1],
                'float'   => [1.23],
                'true'    => [true],
                'false'   => [false],
                'array'   => [[]],
                'object'  => [new stdClass],
                'null'    => [null],
                'string'  => ['ABC'],
            ]
        );

    }

    public static function notInteger()
    {
        return array_merge(
            self::emptyString(),
            [
                'float'  => [1.234],
                'true'   => [true],
                'false'  => [false],
                'array'  => [[]],
                'object' => [new stdClass()],
                'null'   => [null],
                'string' => ['string'],
            ]
        );
    }

    public static function notString()
    {
        return [
            'integer' => [1],
            'float'   => [1.234],
            'true'    => [true],
            'false'   => [false],
            'array'   => [[]],
            'object'  => [new stdClass()],
            'null'    => [null],
        ];
    }

    public static function notBoolean()
    {
        return array_merge(
            self::emptyString(),
            [
                'integer' => [1],
                'float'   => [1.234],
                'array'   => [[]],
                'object'  => [new stdClass()],
                'null'    => [null],
                'string'  => ['string'],
            ]
        );
    }

    public static function notArray()
    {
        return array_merge(
            self::emptyString(),
            [
                'integer' => [1],
                'float'   => [1.234],
                'true'    => [true],
                'false'   => [false],
                'object'  => [new stdClass()],
                'null'    => [null],
                'string'  => ['string'],
            ]
        );
    }

    public static function notCallable()
    {
        return array_merge(
            self::emptyString(),
            [
                'integer' => [1],
                'float'   => [1.234],
                'array'   => [[]],
                'true'    => [true],
                'false'   => [false],
                'object'  => [new stdClass()],
                'null'    => [null],
                'string'  => ['string'],
            ]
        );
    }

    public static function notStringOrEmptyString()
    {
        return array_merge(
            self::notString(),
            self::emptyString()
        );
    }

    public static function notNullAndNotStringOrEmptyString()
    {
        return array_filter(
            self::notStringOrEmptyString(),
            function ($value) {
                return reset($value) !== null;
            }
        );
    }

    public static function emptyString()
    {
        return [
            'empty string'    => [''],
            'new line only'   => ["\n"],
            'only whitespace' => ['   '],
            'nullbyte'        => [chr(0)],
        ];
    }

    public static function notStringOrInteger()
    {
        return [
            'float'  => [1.234],
            'true'   => [true],
            'false'  => [false],
            'array'  => [[]],
            'object' => [new stdClass()],
            'null'   => [null],
        ];
    }
}
