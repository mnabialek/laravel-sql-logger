<?php

namespace Mnabialek\LaravelSqlLogger\Tests;

use Mnabialek\LaravelSqlLogger\Objects\SqlQuery;
use Mnabialek\LaravelSqlLogger\Query;
use Mnabialek\LaravelVersion\Version;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use stdClass;

class QueryTest extends UnitTestCase
{
    #[Test]
    public function it_returns_valid_sql_query_object_when_version_is_below_5_2_0()
    {
        $version = Mockery::mock(Version::class);
        $version->shouldReceive('min')->once()->with('5.2.0')->andReturn(false);

        $queryObject = new Query($version);

        $number = 100;
        $query = 'SELECT * FROM everywhere WHERE user = ?';
        $bindings = ['one', 2, 'three'];
        $time = 516.32;
        $connection = 'db';

        $result = $queryObject->get($number, $query, $bindings, $time, $connection);

        $this->assertInstanceOf(SqlQuery::class, $result);
        $this->assertSame($number, $result->number());
        $this->assertSame($query, $result->raw());
        $this->assertSame($bindings, $result->bindings());
        $this->assertSame($time, $result->time());
        $this->assertSame($connection, $result->connection());
    }

    #[Test]
    public function it_returns_valid_sql_query_object_when_version_is_5_2_0()
    {
        $version = Mockery::mock(Version::class);
        $version->shouldReceive('min')->once()->with('5.2.0')->andReturn(true);

        $queryObject = new Query($version);

        $number = 100;
        $dataObject = new stdClass();
        $dataObject->sql = 'SELECT * FROM everywhere WHERE user = ?';
        $dataObject->bindings = ['one', 2, 'three'];
        $dataObject->time = 516.32;
        $dataObject->connectionName = 'db';

        $result = $queryObject->get($number, $dataObject);

        $this->assertInstanceOf(SqlQuery::class, $result);
        $this->assertSame($number, $result->number());
        $this->assertSame($dataObject->sql, $result->raw());
        $this->assertSame($dataObject->bindings, $result->bindings());
        $this->assertSame($dataObject->time, $result->time());
    }

    #[Test]
    public function it_returns_valid_sql_query_object_when_bindings_are_null()
    {
        $version = Mockery::mock(Version::class);
        $version->shouldReceive('min')->once()->with('5.2.0')->andReturn(true);

        $queryObject = new Query($version);

        $number = 100;
        $dataObject = new stdClass();
        $dataObject->sql = 'SELECT * FROM everywhere WHERE user = ?';
        $dataObject->bindings = null;
        $dataObject->time = 516.32;
        $dataObject->connectionName = 'db';

        $result = $queryObject->get($number, $dataObject);

        $this->assertInstanceOf(SqlQuery::class, $result);
        $this->assertSame($number, $result->number());
        $this->assertSame($dataObject->sql, $result->raw());
        $this->assertSame([], $result->bindings());
        $this->assertSame($dataObject->time, $result->time());
    }
}
