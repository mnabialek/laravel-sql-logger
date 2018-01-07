<?php

namespace Mnabialek\LaravelSqlLogger\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;

class UnitTestCase extends TestCase
{
    public function tearDown()
    {
        m::close();
    }
}
