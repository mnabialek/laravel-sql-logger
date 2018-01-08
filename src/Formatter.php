<?php

namespace Mnabialek\LaravelSqlLogger;

use Carbon\Carbon;
use Mnabialek\LaravelSqlLogger\Objects\SqlQuery;

class Formatter
{
    /**
     * @var Config
     */
    private $config;

    /**
     * Formatter constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Get formatted line.
     *
     * @param SqlQuery $query
     *
     * @return string
     */
    public function getLine(SqlQuery $query)
    {
        return '/* Query ' . $query->number() . ' - ' . Carbon::now()->toDateTimeString() . ' [' .
            $this->time($query->time()) . ']' . " */\n" . $query->get() . ";\n/*" .
            str_repeat('=', 50) . "*/\n";
    }

    /**
     * Format time.
     *
     * @param float $time
     *
     * @return string
     */
    protected function time($time)
    {
        return $this->config->useSeconds() ? ($time / 1000.0) . 's' : $time . 'ms';
    }
}
