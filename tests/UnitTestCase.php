<?php

namespace Mnabialek\LaravelSqlLogger\Tests;

use Carbon\Carbon;
use Mockery;
use PHPUnit\Framework\TestCase;

class UnitTestCase extends TestCase
{
    protected function tearDown()
    {
        Mockery::close();
        Carbon::setTestNow();
    }
}
