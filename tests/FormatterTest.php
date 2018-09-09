<?php

namespace Mnabialek\LaravelSqlLogger\Tests;

use ArrayAccess;
use Carbon\Carbon;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Mnabialek\LaravelSqlLogger\Config;
use Mnabialek\LaravelSqlLogger\Formatter;
use Mnabialek\LaravelSqlLogger\Objects\SqlQuery;
use Mockery;

class FormatterTest extends UnitTestCase
{
    /** @test */
    public function it_formats_line_in_valid_way_when_milliseconds_are_used_and_running_via_http()
    {
        $config = Mockery::mock(Config::class);
        $app = Mockery::mock(Container::class, ArrayAccess::class);
        $config->shouldReceive('useSeconds')->once()->withNoArgs()->andReturn(false);
        $config->shouldReceive('newLinesToSpaces')->once()->withNoArgs()->andReturn(true);
        $config->shouldReceive('entryFormat')->once()->withNoArgs()
            ->andReturn('/* [origin]\n   Query [query_nr] - [datetime] [[query_time]] */\n[query]\n[separator]\n');
        $app->shouldReceive('runningInConsole')->once()->withNoArgs()->andReturn(false);
        $request = Mockery::mock(Request::class);
        $app->shouldReceive('offsetGet')->times(2)->with('request')->andReturn($request);
        $request->shouldReceive('method')->once()->withNoArgs()->andReturn('DELETE');
        $request->shouldReceive('fullUrl')->once()->withNoArgs()
            ->andReturn('http://example.com/test');

        $now = '2015-03-04 08:12:07';
        Carbon::setTestNow($now);

        $query = Mockery::mock(SqlQuery::class);
        $number = 434;
        $time = 617.24;
        $sql = 'SELECT * FROM somewhere';
        $query->shouldReceive('number')->once()->withNoArgs()->andReturn($number);
        $query->shouldReceive('get')->once()->withNoArgs()->andReturn($sql);
        $query->shouldReceive('time')->once()->withNoArgs()->andReturn($time);

        $formatter = new Formatter($app, $config);
        $result = $formatter->getLine($query);

        $expected = <<<EOT
/* Origin (request): DELETE http://example.com/test
   Query {$number} - {$now} [{$time}ms] */
{$sql};
/*==================================================*/

EOT;

        $this->assertSame($expected, $result);
    }

    /** @test */
    public function it_formats_line_in_valid_way_when_custom_entry_format_was_used()
    {
        $config = Mockery::mock(Config::class);
        $app = Mockery::mock(Container::class, ArrayAccess::class);
        $config->shouldReceive('useSeconds')->once()->withNoArgs()->andReturn(false);
        $config->shouldReceive('newLinesToSpaces')->once()->withNoArgs()->andReturn(true);
        $config->shouldReceive('entryFormat')->once()->withNoArgs()
            ->andReturn("[separator]\n[query_nr] : [datetime] [query_time]\n[origin]\n[query]\n[separator]\n");
        $app->shouldReceive('runningInConsole')->once()->withNoArgs()->andReturn(false);
        $request = Mockery::mock(Request::class);
        $app->shouldReceive('offsetGet')->times(2)->with('request')->andReturn($request);
        $request->shouldReceive('method')->once()->withNoArgs()->andReturn('DELETE');
        $request->shouldReceive('fullUrl')->once()->withNoArgs()
            ->andReturn('http://example.com/test');

        $now = '2015-03-04 08:12:07';
        Carbon::setTestNow($now);

        $query = Mockery::mock(SqlQuery::class);
        $number = 434;
        $time = 617.24;
        $sql = 'SELECT * FROM somewhere';
        $query->shouldReceive('number')->once()->withNoArgs()->andReturn($number);
        $query->shouldReceive('get')->once()->withNoArgs()->andReturn($sql);
        $query->shouldReceive('time')->once()->withNoArgs()->andReturn($time);

        $formatter = new Formatter($app, $config);
        $result = $formatter->getLine($query);

        $expected = <<<EOT
/*==================================================*/
{$number} : {$now} {$time}ms
Origin (request): DELETE http://example.com/test
{$sql};
/*==================================================*/

EOT;

        $this->assertSame($expected, $result);
    }

    /** @test */
    public function it_formats_line_in_valid_way_when_seconds_are_used_and_running_via_http()
    {
        $config = Mockery::mock(Config::class);
        $app = Mockery::mock(Container::class, ArrayAccess::class);
        $config->shouldReceive('useSeconds')->once()->withNoArgs()->andReturn(true);
        $config->shouldReceive('newLinesToSpaces')->once()->withNoArgs()->andReturn(true);
        $config->shouldReceive('entryFormat')->once()->withNoArgs()
            ->andReturn('/* [origin]\n   Query [query_nr] - [datetime] [[query_time]] */\n[query]\n[separator]\n');
        $app->shouldReceive('runningInConsole')->once()->withNoArgs()->andReturn(false);
        $request = Mockery::mock(Request::class);
        $app->shouldReceive('offsetGet')->times(2)->with('request')->andReturn($request);
        $request->shouldReceive('method')->once()->withNoArgs()->andReturn('GET');
        $request->shouldReceive('fullUrl')->once()->withNoArgs()
            ->andReturn('https://example.com/test');

        $now = '2015-03-04 08:12:07';
        Carbon::setTestNow($now);

        $query = Mockery::mock(SqlQuery::class);
        $number = 434;
        $time = 617.24;
        $sql = 'SELECT * FROM somewhere';
        $query->shouldReceive('number')->once()->withNoArgs()->andReturn($number);
        $query->shouldReceive('get')->once()->withNoArgs()->andReturn($sql);
        $query->shouldReceive('time')->once()->withNoArgs()->andReturn($time);

        $formatter = new Formatter($app, $config);
        $result = $formatter->getLine($query);

        $expected = <<<EOT
/* Origin (request): GET https://example.com/test
   Query {$number} - {$now} [0.61724s] */
{$sql};
/*==================================================*/

EOT;

        $this->assertSame($expected, $result);
    }

    /** @test */
    public function it_formats_line_in_valid_way_when_milliseconds_are_used_and_running_via_console()
    {
        $config = Mockery::mock(Config::class);
        $app = Mockery::mock(Container::class, ArrayAccess::class);
        $config->shouldReceive('useSeconds')->once()->withNoArgs()->andReturn(false);
        $config->shouldReceive('newLinesToSpaces')->once()->withNoArgs()->andReturn(true);
        $config->shouldReceive('entryFormat')->once()->withNoArgs()
            ->andReturn('/* [origin]\n   Query [query_nr] - [datetime] [[query_time]] */\n[query]\n[separator]\n');
        $app->shouldReceive('runningInConsole')->once()->withNoArgs()->andReturn(true);
        $request = Mockery::mock(Request::class);
        $app->shouldReceive('offsetGet')->once()->with('request')->andReturn($request);
        $request->shouldReceive('server')->once()->with('argv', [])->andReturn('php artisan test');

        $now = '2015-03-04 08:12:07';
        Carbon::setTestNow($now);

        $query = Mockery::mock(SqlQuery::class);
        $number = 434;
        $time = 617.24;
        $sql = 'SELECT * FROM somewhere';
        $query->shouldReceive('number')->once()->withNoArgs()->andReturn($number);
        $query->shouldReceive('get')->once()->withNoArgs()->andReturn($sql);
        $query->shouldReceive('time')->once()->withNoArgs()->andReturn($time);

        $formatter = new Formatter($app, $config);
        $result = $formatter->getLine($query);

        $expected = <<<EOT
/* Origin (console): php artisan test
   Query {$number} - {$now} [{$time}ms] */
{$sql};
/*==================================================*/

EOT;

        $this->assertSame($expected, $result);
    }

    /** @test */
    public function it_formats_line_in_valid_way_when_milliseconds_are_used_and_running_via_console_for_array()
    {
        $config = Mockery::mock(Config::class);
        $app = Mockery::mock(Container::class, ArrayAccess::class);
        $config->shouldReceive('useSeconds')->once()->withNoArgs()->andReturn(false);
        $config->shouldReceive('newLinesToSpaces')->once()->withNoArgs()->andReturn(true);
        $config->shouldReceive('entryFormat')->once()->withNoArgs()
            ->andReturn('/* [origin]\n   Query [query_nr] - [datetime] [[query_time]] */\n[query]\n[separator]\n');
        $app->shouldReceive('runningInConsole')->once()->withNoArgs()->andReturn(true);
        $request = Mockery::mock(Request::class);
        $app->shouldReceive('offsetGet')->once()->with('request')->andReturn($request);
        $request->shouldReceive('server')->once()->with('argv', [])->andReturn([
            'php',
            'artisan',
            'test',
        ]);

        $now = '2015-03-04 08:12:07';
        Carbon::setTestNow($now);

        $query = Mockery::mock(SqlQuery::class);
        $number = 434;
        $time = 617.24;
        $sql = 'SELECT * FROM somewhere';
        $query->shouldReceive('number')->once()->withNoArgs()->andReturn($number);
        $query->shouldReceive('get')->once()->withNoArgs()->andReturn($sql);
        $query->shouldReceive('time')->once()->withNoArgs()->andReturn($time);

        $formatter = new Formatter($app, $config);
        $result = $formatter->getLine($query);

        $expected = <<<EOT
/* Origin (console): php artisan test
   Query {$number} - {$now} [{$time}ms] */
{$sql};
/*==================================================*/

EOT;

        $this->assertSame($expected, $result);
    }

    /** @test */
    public function it_replaces_new_lines_in_query_by_spaces_when_config_set_to_true()
    {
        $config = Mockery::mock(Config::class);
        $app = Mockery::mock(Container::class, ArrayAccess::class);
        $config->shouldReceive('useSeconds')->once()->withNoArgs()->andReturn(false);
        $config->shouldReceive('newLinesToSpaces')->once()->withNoArgs()->andReturn(true);
        $config->shouldReceive('entryFormat')->once()->withNoArgs()
            ->andReturn('/* [origin]\n   Query [query_nr] - [datetime] [[query_time]] */\n[query]\n[separator]\n');
        $app->shouldReceive('runningInConsole')->once()->withNoArgs()->andReturn(false);
        $request = Mockery::mock(Request::class);
        $app->shouldReceive('offsetGet')->times(2)->with('request')->andReturn($request);
        $request->shouldReceive('method')->once()->withNoArgs()->andReturn('DELETE');
        $request->shouldReceive('fullUrl')->once()->withNoArgs()
            ->andReturn('http://example.com/test');

        $now = '2015-03-04 08:12:07';
        Carbon::setTestNow($now);

        $query = Mockery::mock(SqlQuery::class);
        $number = 434;
        $time = 617.24;
        $sql = <<<SQL
SELECT * FROM 
somewhere WHERE name = '
'
SQL;

        $expectedSql = <<<SQL
SELECT * FROM  somewhere WHERE name = '
'
SQL;

        $query->shouldReceive('number')->once()->withNoArgs()->andReturn($number);
        $query->shouldReceive('get')->once()->withNoArgs()->andReturn($sql);
        $query->shouldReceive('time')->once()->withNoArgs()->andReturn($time);

        $formatter = new Formatter($app, $config);
        $result = $formatter->getLine($query);

        $expected = <<<EOT
/* Origin (request): DELETE http://example.com/test
   Query {$number} - {$now} [{$time}ms] */
{$expectedSql};
/*==================================================*/

EOT;

        $this->assertSame($expected, $result);
    }

    /** @test */
    public function it_does_not_replace_new_lines_in_query_when_config_set_to_false()
    {
        $config = Mockery::mock(Config::class);
        $app = Mockery::mock(Container::class, ArrayAccess::class);
        $config->shouldReceive('useSeconds')->once()->withNoArgs()->andReturn(false);
        $config->shouldReceive('newLinesToSpaces')->once()->withNoArgs()->andReturn(false);
        $config->shouldReceive('entryFormat')->once()->withNoArgs()
            ->andReturn('/* [origin]\n   Query [query_nr] - [datetime] [[query_time]] */\n[query]\n[separator]\n');
        $app->shouldReceive('runningInConsole')->once()->withNoArgs()->andReturn(false);
        $request = Mockery::mock(Request::class);
        $app->shouldReceive('offsetGet')->times(2)->with('request')->andReturn($request);
        $request->shouldReceive('method')->once()->withNoArgs()->andReturn('DELETE');
        $request->shouldReceive('fullUrl')->once()->withNoArgs()
            ->andReturn('http://example.com/test');

        $now = '2015-03-04 08:12:07';
        Carbon::setTestNow($now);

        $query = Mockery::mock(SqlQuery::class);
        $number = 434;
        $time = 617.24;
        $sql = <<<SQL
SELECT * FROM 
somewhere WHERE name = '
'
SQL;

        $expectedSql = <<<SQL
SELECT * FROM 
somewhere WHERE name = '
'
SQL;

        $query->shouldReceive('number')->once()->withNoArgs()->andReturn($number);
        $query->shouldReceive('get')->once()->withNoArgs()->andReturn($sql);
        $query->shouldReceive('time')->once()->withNoArgs()->andReturn($time);

        $formatter = new Formatter($app, $config);
        $result = $formatter->getLine($query);

        $expected = <<<EOT
/* Origin (request): DELETE http://example.com/test
   Query {$number} - {$now} [{$time}ms] */
{$expectedSql};
/*==================================================*/

EOT;

        $this->assertSame($expected, $result);
    }
}
