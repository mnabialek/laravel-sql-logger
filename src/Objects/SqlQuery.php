<?php

namespace Mnabialek\LaravelSqlLogger\Objects;

use Mnabialek\LaravelSqlLogger\Objects\Concerns\ReplacesBindings;

class SqlQuery
{
    use ReplacesBindings;

    /**
     * @var int
     */
    private $number;

    /**
     * @var string
     */
    private $sql;

    /**
     * @var array
     */
    private $bindings;

    /**
     * @var float
     */
    private $time;

    /**
     * SqlQuery constructor.
     *
     * @param int $number
     * @param string $sql
     * @param array|null $bindings
     * @param float $time
     */
    public function __construct($number, $sql, array $bindings = null, $time)
    {
        $this->number = $number;
        $this->sql = $sql;
        $this->bindings = $bindings ?: [];
        $this->time = $time;
    }

    /**
     * Get number of query.
     *
     * @return int
     */
    public function number()
    {
        return $this->number;
    }

    /**
     * Get raw SQL (without bindings).
     *
     * @return string
     */
    public function raw()
    {
        return $this->sql;
    }

    /**
     * Get bindings.
     *
     * @return array
     */
    public function bindings()
    {
        return $this->bindings;
    }

    /**
     * Get time.
     *
     * @return float
     */
    public function time()
    {
        return $this->time;
    }

    /**
     * Get full query with values from bindings inserted.
     *
     * @return string
     */
    public function get()
    {
        return $this->replaceBindings($this->sql, $this->bindings);
    }
}
