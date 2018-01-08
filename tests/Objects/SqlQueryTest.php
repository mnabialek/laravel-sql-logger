<?php

namespace Mnabialek\LaravelSqlLogger\Tests\Objects;

use Illuminate\Support\Carbon;
use Mnabialek\LaravelSqlLogger\Objects\SqlQuery;
use Mnabialek\LaravelSqlLogger\Tests\UnitTestCase;

class SqlQueryTest extends UnitTestCase
{
    /** @test */
    public function it_returns_valid_number()
    {
        $value = 56;
        $query = new SqlQuery($value, 'test', [], 130);
        $this->assertSame($value, $query->number());
    }

    /** @test */
    public function it_returns_valid_raw_query()
    {
        $value = 'SELECT * FROM tests WHERE a = ?';
        $query = new SqlQuery(56, $value, ['test'], 130);
        $this->assertSame($value, $query->raw());
    }

    /** @test */
    public function it_returns_valid_bindings_array()
    {
        $value = ['one', new \DateTime(), 3];
        $query = new SqlQuery(56, 'test', $value, 130);
        $this->assertSame($value, $query->bindings());
    }

    /** @test */
    public function it_returns_valid_time()
    {
        $value = 130;
        $query = new SqlQuery(56, 'test', [], $value);
        $this->assertSame($value, $query->time());
    }

    /** @test */
    public function it_returns_valid_query_with_replaced_bindings()
    {
        $sql = "SELECT * FROM tests WHERE a = ? AND CONCAT(?, '%'" . "\n" . ', ?) = ?';
        $bindings = ["'test", Carbon::yesterday(), new \DateTime('tomorrow'), 453];
        $query = new SqlQuery(56, $sql, $bindings, 130);
        $this->assertSame("SELECT * FROM tests WHERE a = '\'test' AND CONCAT('" .
            $bindings[1]->toDateTimeString() . "', '%' , '" .
            $bindings[2]->format('Y-m-d H:i:s') . "') = '453'", $query->get());
    }
}
