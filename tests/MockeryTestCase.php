<?php

namespace Ibuildings\QaTools\Test;

use Mockery;
use PHPUnit\Framework\TestCase;

abstract class MockeryTestCase extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();

        Mockery::close();
    }
}
