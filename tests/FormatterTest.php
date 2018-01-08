<?php

namespace Mnabialek\LaravelSqlLogger\Tests;

use Carbon\Carbon;
use Mnabialek\LaravelSqlLogger\Config;
use Mnabialek\LaravelSqlLogger\Formatter;
use Mnabialek\LaravelSqlLogger\Objects\SqlQuery;
use Mockery;

class FormatterTest extends UnitTestCase
{
    /** @test */
    public function it_formats_line_in_walid_way_when_milliseconds_are_used()
    {
        $config = Mockery::mock(Config::class);
        $config->shouldReceive('useSeconds')->once()->withNoArgs()->andReturn(false);

        $now = '2015-03-04 08:12:07';
        Carbon::setTestNow($now);

        $query = Mockery::mock(SqlQuery::class);
        $number = 434;
        $time = 617.24;
        $sql = 'SELECT * FROM somewhere';
        $query->shouldReceive('number')->once()->withNoArgs()->andReturn($number);
        $query->shouldReceive('get')->once()->withNoArgs()->andReturn($sql);
        $query->shouldReceive('time')->once()->withNoArgs()->andReturn($time);

        $formatter = new Formatter($config);
        $result = $formatter->getLine($query);

        $expected = <<<EOT
/* Query {$number} - {$now} [{$time}ms] */
{$sql};
/*==================================================*/

EOT;

        $this->assertSame($expected, $result);
    }

    /** @test */
    public function it_formats_line_in_walid_way_when_seconds_are_used()
    {
        $config = Mockery::mock(Config::class);
        $config->shouldReceive('useSeconds')->once()->withNoArgs()->andReturn(true);

        $now = '2015-03-04 08:12:07';
        Carbon::setTestNow($now);

        $query = Mockery::mock(SqlQuery::class);
        $number = 434;
        $time = 617.24;
        $sql = 'SELECT * FROM somewhere';
        $query->shouldReceive('number')->once()->withNoArgs()->andReturn($number);
        $query->shouldReceive('get')->once()->withNoArgs()->andReturn($sql);
        $query->shouldReceive('time')->once()->withNoArgs()->andReturn($time);

        $formatter = new Formatter($config);
        $result = $formatter->getLine($query);

        $expected = <<<EOT
/* Query {$number} - {$now} [0.61724s] */
{$sql};
/*==================================================*/

EOT;

        $this->assertSame($expected, $result);
    }
}
