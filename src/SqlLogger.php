<?php

namespace Mnabialek\LaravelSqlLogger;

use Exception;
use Illuminate\Container\Container;

class SqlLogger
{
    /**
     * @var Container
     */
    private $app;

    /**
     * @var Query
     */
    private $query;

    /**
     * @var Writer
     */
    private $writer;

    /**
     * Number of executed queries.
     *
     * @var int
     */
    private $queryNumber = 0;

    /**
     * SqlLogger constructor.
     *
     * @param \Illuminate\Container\Container $app
     * @param Query $query
     * @param Writer $writer
     */
    public function __construct(Container $app, Query $query, Writer $writer)
    {
        $this->app = $app;
        $this->query = $query;
        $this->writer = $writer;
    }

    /**
     * Log query.
     *
     * @param string|\Illuminate\Database\Events\QueryExecuted $query
     * @param array|null $bindings
     * @param float|null $time
     */
    public function log($query, array $bindings = null, $time = null)
    {
        ++$this->queryNumber;

        try {
            $sqlQuery = $this->query->get($this->queryNumber, $query, $bindings, $time);
            $this->writer->save($sqlQuery);
        } catch (Exception $e) {
            $this->app['log']->notice("Cannot log query nr {$this->queryNumber}. Exception:" . PHP_EOL . $e);
        }
    }
}
