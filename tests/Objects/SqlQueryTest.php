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
        $sql = "SELECT * FROM tests WHERE a = ? AND CONCAT(?, '%'" . "\n" . ', ?) = ? AND column = ?';
        $bindings = ["'test", Carbon::yesterday(), new \DateTime('tomorrow'), 453, 67.23];
        $query = new SqlQuery(56, $sql, $bindings, 130);
        $this->assertSame("SELECT * FROM tests WHERE a = '\'test' AND CONCAT('" .
            $bindings[1]->toDateTimeString() . "', '%' , '" .
            $bindings[2]->format('Y-m-d H:i:s') . "') = 453 AND column = 67.23", $query->get());
    }

    /** @test */
    public function it_returns_valid_query_when_question_mark_in_quotes()
    {
        $sql = <<<EOF
SELECT * FROM tests WHERE a = '?' AND b = "?" AND c = ? AND D = '\\?' AND e = "\"?" AND f = ?;
EOF;
        $bindings = ["'test", 52];
        $query = new SqlQuery(56, $sql, $bindings, 130);

        $expectedSql = <<<EOF
SELECT * FROM tests WHERE a = '?' AND b = "?" AND c = '\'test' AND D = '\\?' AND e = "\"?" AND f = 52;
EOF;

        $this->assertSame($expectedSql, $query->get());
    }

    /** @test */
    public function it_returns_valid_query_for_named_bindings()
    {
        $sql = <<<EOF
SELECT * FROM tests WHERE a = ? AND b = :email AND c = ? AND D = :something AND true;
EOF;
        $bindings = ["'test", 52, 'example', 53, 77];
        $query = new SqlQuery(56, $sql, $bindings, 130);

        $expectedSql = <<<EOF
SELECT * FROM tests WHERE a = '\'test' AND b = 52 AND c = 'example' AND D = 53 AND true;
EOF;

        $this->assertSame($expectedSql, $query->get());
    }
}
