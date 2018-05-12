<?php

namespace Mnabialek\LaravelSqlLogger\Tests;

use Carbon\Carbon;
use Illuminate\Container\Container;
use Mnabialek\LaravelSqlLogger\Config;
use Mnabialek\LaravelSqlLogger\FileName;
use Mockery;

class FileNameTest extends UnitTestCase
{
    /**
     * @var Container|\Mockery\Mock
     */
    protected $app;

    /**
     * @var Config|\Mockery\Mock
     */
    protected $config;

    /**
     * @var FileName
     */
    protected $filename;

    protected function setUp()
    {
        Carbon::setTestNow('2015-03-07 08:16:09');
        $this->app = Mockery::mock(Container::class);
        $this->config = Mockery::mock(Config::class);
        $this->filename = new FileName($this->app, $this->config);
    }

    /** @test */
    public function it_returns_valid_file_name_for_all_queries_when_not_running_in_console()
    {
        $this->app->shouldReceive('runningInConsole')->once()->withNoArgs()->andReturn(false);
        $this->config->shouldReceive('allQueriesFileName')->once()->withNoArgs()
            ->andReturn('sample[Y]-test-[m]-abc-[d]');
        $this->config->shouldReceive('fileExtension')->once()->withNoArgs()
            ->andReturn('.extension');
        $result = $this->filename->getForAllQueries();
        $this->assertSame('sample2015-test-03-abc-07.extension', $result);
    }

    /** @test */
    public function it_returns_valid_file_name_for_all_queries_when_running_in_console()
    {
        $this->app->shouldReceive('runningInConsole')->once()->withNoArgs()->andReturn(true);
        $this->config->shouldReceive('consoleSuffix')->once()->withNoArgs()
            ->andReturn('-artisan-suffix');
        $this->config->shouldReceive('allQueriesFileName')->once()->withNoArgs()
            ->andReturn('sample[Y]-test-[m]-abc-[d]');
        $this->config->shouldReceive('fileExtension')->once()->withNoArgs()
            ->andReturn('.extension');
        $result = $this->filename->getForAllQueries();
        $this->assertSame('sample2015-test-03-abc-07-artisan-suffix.extension', $result);
    }

    /** @test */
    public function it_returns_valid_file_name_for_slow_queries_when_not_running_in_console()
    {
        $this->app->shouldReceive('runningInConsole')->once()->withNoArgs()->andReturn(false);
        $this->config->shouldReceive('slowQueriesFileName')->once()->withNoArgs()
            ->andReturn('[Y]-test-[m]-abc-[d]-sample');
        $this->config->shouldReceive('fileExtension')->once()->withNoArgs()
            ->andReturn('.log');
        $result = $this->filename->getForSlowQueries();
        $this->assertSame('2015-test-03-abc-07-sample.log', $result);
    }

    /** @test */
    public function it_returns_valid_file_name_for_slow_queries_when_running_in_console()
    {
        $this->app->shouldReceive('runningInConsole')->once()->withNoArgs()->andReturn(true);
        $this->config->shouldReceive('consoleSuffix')->once()->withNoArgs()
            ->andReturn('-artisan-suffix');
        $this->config->shouldReceive('slowQueriesFileName')->once()->withNoArgs()
            ->andReturn('sample[Y]-test-[m]-abc-[d]-slow');
        $this->config->shouldReceive('fileExtension')->once()->withNoArgs()
            ->andReturn('.log');
        $result = $this->filename->getForSlowQueries();
        $this->assertSame('sample2015-test-03-abc-07-slow-artisan-suffix.log', $result);
    }
}
