<?php

namespace Mnabialek\LaravelSqlLogger\Objects;

use DateTime;

class SqlQuery
{
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
     * @param array $bindings
     * @param float $time
     */
    public function __construct($number, $sql, array $bindings, $time)
    {
        $this->number = $number;
        $this->sql = $sql;
        $this->bindings = $bindings;
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
        return vsprintf(str_replace(['%', '?', "\n"], ['%%', "'%s'", ' '], $this->sql),
            $this->formatBindings($this->bindings));
    }

    /**
     * Format bindings values.
     * 
     * @param array $bindings
     *
     * @return array
     */
    protected function formatBindings($bindings)
    {
        foreach ($bindings as $key => $binding) {
            if ($binding instanceof DateTime) {
                $bindings[$key] = $binding->format('Y-m-d H:i:s');
            } elseif (is_string($binding)) {
                $bindings[$key] = str_replace("'", "\\'", $binding);
            }
        }

        return $bindings;
    }
}
