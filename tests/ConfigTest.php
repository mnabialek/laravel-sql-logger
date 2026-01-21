<?php
namespace Mnabialek\LaravelSqlLogger\Tests;

use Illuminate\Contracts\Config\Repository;
use Mnabialek\LaravelSqlLogger\Config;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class ConfigTest extends UnitTestCase
{
    /**
     * @var Repository|\Mockery\Mock
     */
    protected $repository;

    /**
     * @var Config|\Mockery\Mock
     */
    protected $config;

    protected function setUp(): void
    {
        $this->repository = Mockery::mock(Repository::class);
        $this->config = new Config($this->repository);
    }

    #[Test]
    public function it_returns_valid_values_for_logAllQueries()
    {
        $this->repository->shouldReceive('get')->once()->with('sql_logger.all_queries.enabled')
            ->andReturn(1);
        $this->assertTrue($this->config->logAllQueries());

        $this->repository->shouldReceive('get')->once()->with('sql_logger.all_queries.enabled')
            ->andReturn(0);
        $this->assertFalse($this->config->logAllQueries());
    }

    #[Test]
    public function it_returns_valid_values_for_logSlowQueries()
    {
        $this->repository->shouldReceive('get')->once()->with('sql_logger.slow_queries.enabled')
            ->andReturn(1);
        $this->assertTrue($this->config->logSlowQueries());

        $this->repository->shouldReceive('get')->once()->with('sql_logger.slow_queries.enabled')
            ->andReturn(0);
        $this->assertFalse($this->config->logSlowQueries());
    }

    #[Test]
    public function it_returns_valid_value_for_slowLogTime()
    {
        $value = '700';
        $this->repository->shouldReceive('get')->once()->with('sql_logger.slow_queries.min_exec_time')
            ->andReturn($value);
        $this->assertSame($value, $this->config->slowLogTime());
    }

    #[Test]
    public function it_returns_valid_values_for_overrideFile()
    {
        $this->repository->shouldReceive('get')->once()->with('sql_logger.all_queries.override_log')
            ->andReturn(1);
        $this->assertTrue($this->config->overrideFile());

        $this->repository->shouldReceive('get')->once()->with('sql_logger.all_queries.override_log')
            ->andReturn(0);
        $this->assertFalse($this->config->overrideFile());
    }

    #[Test]
    public function it_returns_valid_value_for_logDirectory()
    {
        $value = 'sample directory';
        $this->repository->shouldReceive('get')->once()->with('sql_logger.general.directory')
            ->andReturn($value);
        $this->assertSame($value, $this->config->logDirectory());
    }

    #[Test]
    public function it_returns_valid_values_for_useSeconds()
    {
        $this->repository->shouldReceive('get')->once()->with('sql_logger.general.use_seconds')
            ->andReturn(1);
        $this->assertTrue($this->config->useSeconds());

        $this->repository->shouldReceive('get')->once()->with('sql_logger.general.use_seconds')
            ->andReturn(0);
        $this->assertFalse($this->config->useSeconds());
    }

    #[Test]
    public function it_returns_valid_values_for_consoleSuffix()
    {
        $this->repository->shouldReceive('get')->once()->with('sql_logger.general.console_log_suffix')
            ->andReturn('-artisan');
        $this->assertSame('-artisan', $this->config->consoleSuffix());

        $this->repository->shouldReceive('get')->once()->with('sql_logger.general.console_log_suffix')
            ->andReturn('');
        $this->assertSame('', $this->config->consoleSuffix());
    }

    #[Test]
    public function it_returns_valid_value_for_allQueriesPattern()
    {
        $value = 'sample pattern';
        $this->repository->shouldReceive('get')->once()->with('sql_logger.all_queries.pattern')
            ->andReturn($value);
        $this->assertSame($value, $this->config->allQueriesPattern());
    }

    #[Test]
    public function it_returns_valid_value_for_slowQueriesPattern()
    {
        $value = 'sample pattern';
        $this->repository->shouldReceive('get')->once()->with('sql_logger.slow_queries.pattern')
            ->andReturn($value);
        $this->assertSame($value, $this->config->slowQueriesPattern());
    }

    #[Test]
    public function it_returns_valid_file_extension()
    {
        $value = '.sql';
        $this->repository->shouldReceive('get')->once()->with('sql_logger.general.extension')
            ->andReturn($value);
        $this->assertSame($value, $this->config->fileExtension());
    }

    #[Test]
    public function it_returns_valid_all_queries_file_name()
    {
        $value = '[Y-m-d]-sample';
        $this->repository->shouldReceive('get')->once()->with('sql_logger.all_queries.file_name')
            ->andReturn($value);
        $this->assertSame($value, $this->config->allQueriesFileName());
    }

    #[Test]
    public function it_returns_valid_slow_queries_file_name()
    {
        $value = '[Y-m-d]-sample';
        $this->repository->shouldReceive('get')->once()->with('sql_logger.slow_queries.file_name')
            ->andReturn($value);
        $this->assertSame($value, $this->config->slowQueriesFileName());
    }

    #[Test]
    public function it_returns_valid_values_for_newLinesToSpaces()
    {
        $this->repository->shouldReceive('get')->once()->with('sql_logger.formatting.new_lines_to_spaces')
            ->andReturn(true);
        $this->assertTrue($this->config->newLinesToSpaces());

        $this->repository->shouldReceive('get')->once()->with('sql_logger.formatting.new_lines_to_spaces')
            ->andReturn(false);
        $this->assertFalse($this->config->newLinesToSpaces());
    }

    #[Test]
    public function it_returns_valid_value_for_entryFormat()
    {
        $this->repository->shouldReceive('get')->once()->with('sql_logger.formatting.entry_format')
            ->andReturn('[sample]/[example]/foo');
        $this->assertSame('[sample]/[example]/foo', $this->config->entryFormat());
    }
}
