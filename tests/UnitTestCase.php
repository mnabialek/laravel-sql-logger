<?php

namespace Mnabialek\LaravelSqlLogger\Tests;

use Carbon\Carbon;
use Mockery;
use PHPUnit\Framework\TestCase;

class UnitTestCase extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
        Carbon::setTestNow();
    }
}
