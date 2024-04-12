<?php

namespace Mnabialek\LaravelSqlLogger\Tests;

use Illuminate\Container\Container;
use Illuminate\Database\Events\QueryExecuted;
use Mnabialek\LaravelSqlLogger\Objects\SqlQuery;
use Mnabialek\LaravelSqlLogger\Query;
use Mnabialek\LaravelSqlLogger\SqlLogger;
use Mnabialek\LaravelSqlLogger\Writer;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use stdClass;

class SqlLoggerTest extends UnitTestCase
{
    /**
     * @var Container|\Mockery\Mock
     */
    private $app;

    /**
     * @var Query|\Mockery\Mock
     */
    private $query;

    /**
     * @var Writer|\Mockery\Mock
     */
    private $writer;

    /**
     * @var SqlLogger
     */
    private $logger;

    protected function setUp(): void
    {
        $this->app = Mockery::mock(Container::class);
        $this->query = Mockery::mock(Query::class);
        $this->writer = Mockery::mock(Writer::class);
        $this->logger = new SqlLogger($this->app, $this->query, $this->writer);
    }

    #[Test]
    public function it_runs_writer_with_valid_query()
    {
        $sql = 'SELECT * FROM somewhere';
        $bindings = ['one', 2];
        $time = 5412;
        $connection = Mockery::mock(stdClass::class);
        $connection->shouldReceive('getName')->withNoArgs()->andReturn("db");
        $query = new QueryExecuted($sql, $bindings, $time, $connection);
        $connectionName = 'db';

        $sqlQuery = new SqlQuery(4, 'anything', [], 3.54, 'db');
        $this->query->shouldReceive('get')->once()->with(1, $query, $bindings, $time, $connectionName)
            ->andReturn($sqlQuery);
        $this->writer->shouldReceive('save')->once()->with($sqlQuery);

        $this->logger->log($query, $bindings, $time);
        $this->assertTrue(true);
    }

    #[Test]
    public function it_uses_valid_query_number_for_multiple_queries()
    {
        $sql = 'SELECT * FROM somewhere';
        $bindings = ['one', 2];
        $time = 5412;
        $connection = Mockery::mock(stdClass::class);
        $connection->shouldReceive('getName')->withNoArgs()->andReturn("db");
        $query = new QueryExecuted($sql, $bindings, $time, $connection);
        $connectionName = 'db';

        $sql2 = 'SELECT * FROM world';
        $bindings2 = ['three', 4];
        $time2 = 45.43;
        $connection2 = Mockery::mock(stdClass::class);
        $connection2->shouldReceive('getName')->withNoArgs()->andReturn("db");
        $query2 = new QueryExecuted($sql2, $bindings2, $time2, $connection2);
        $connectionName2 = 'db';

        $sqlQuery = new SqlQuery(4, 'anything', [], 3.54, 'db');
        $this->query->shouldReceive('get')->once()->with(1, $query, $bindings, $time, $connectionName )
            ->andReturn($sqlQuery);
        $this->writer->shouldReceive('save')->once()->with($sqlQuery);

        $sqlQuery2 = new SqlQuery(6, 'anything2', [], 41.23, 'db');
        $this->query->shouldReceive('get')->once()->with(2, $query2, $bindings2, $time2, $connectionName2)
            ->andReturn($sqlQuery2);
        $this->writer->shouldReceive('save')->once()->with($sqlQuery2);

        $this->logger->log($query, $bindings, $time);
        $this->logger->log($query2, $bindings2, $time2);
        $this->assertTrue(true);
    }

    #[Test]
    public function it_logs_thrown_exception_and_continue_working_for_next_query()
    {
        $sql = 'SELECT * FROM somewhere';
        $bindings = ['one', 2];
        $time = 5412;
        $connection = Mockery::mock(stdClass::class);
        $connection->shouldReceive('getName')->withNoArgs()->andReturn("db");
        $query = new QueryExecuted($sql, $bindings, $time, $connection);
        $connectionName = 'db';

        $sql2 = 'SELECT * FROM world';
        $bindings2 = ['three', 4];
        $time2 = 45.43;
        $connection2 = Mockery::mock(stdClass::class);
        $connection2->shouldReceive('getName')->withNoArgs()->andReturn("db");
        $query2 = new QueryExecuted($sql2, $bindings2, $time2, $connection2);
        $connectionName2 = 'db';

        $exception = new \Exception('Sample message');

        $sqlQuery = new SqlQuery(4, 'anything', [], 3.54, 'db');
        $this->query->shouldReceive('get')->once()->with(1, $query, $bindings, $time, $connectionName)
            ->andReturn($sqlQuery);
        $this->writer->shouldReceive('save')->once()->with($sqlQuery)->andThrow($exception);

        $log = Mockery::mock(stdClass::class);
        $this->app->shouldReceive('offsetGet')->once()->with('log')->andReturn($log);
        $log->shouldReceive('notice')->once()->with("Cannot log query nr 1. Exception:\n" . $exception);

        $sqlQuery2 = new SqlQuery(6, 'anything2', [], 41.23, 'db');
        $this->query->shouldReceive('get')->once()->with(2, $query2, $bindings2, $time2, $connectionName2)
            ->andReturn($sqlQuery2);
        $this->writer->shouldReceive('save')->once()->with($sqlQuery2);

        $this->logger->log($query, $bindings, $time);
        $this->logger->log($query2, $bindings2, $time2);
        $this->assertTrue(true);
    }
}
