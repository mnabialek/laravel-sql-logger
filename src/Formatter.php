<?php

namespace Mnabialek\LaravelSqlLogger;

use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Mnabialek\LaravelSqlLogger\Objects\SqlQuery;

class Formatter
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var Config
     */
    private $config;

    /**
     * Formatter constructor.
     *
     * @param Application $app
     * @param Config $config
     */
    public function __construct(Application $app, Config $config)
    {
        $this->app = $app;
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
        return '/* ' . $this->originLine() . PHP_EOL .
            '   ' . $this->querySummaryLine($query) . ' */' . PHP_EOL .
            $this->queryLine($query) . PHP_EOL .
            $this->separatorLine() . PHP_EOL;
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

    /**
     * Get origin line.
     *
     * @return string
     */
    protected function originLine()
    {
        return 'Origin ' . ($this->app->runningInConsole()
                ? '(console): ' . $this->getArtisanLine()
                : '(request): ' . $this->getRequestLine());
    }

    /**
     * Get query summary line.
     *
     * @param SqlQuery $query
     *
     * @return string
     */
    protected function querySummaryLine(SqlQuery $query)
    {
        return 'Query ' . $query->number() . ' - ' . Carbon::now()->toDateTimeString() . ' [' .
            $this->time($query->time()) . ']';
    }

    /**
     * Get query line.
     *
     * @param SqlQuery $query
     *
     * @return string
     */
    protected function queryLine(SqlQuery $query)
    {
        return $query->get() . ';';
    }

    /**
     * Get Artisan line.
     *
     * @return string
     */
    protected function getArtisanLine()
    {
        $command = $this->app['request']->server('argv', []);

        if (is_array($command)) {
            $command = implode(' ', $command);
        }

        return $command;
    }

    /**
     * Get request line.
     *
     * @return string
     */
    protected function getRequestLine()
    {
        return $this->app['request']->method() . ' ' . $this->app['request']->fullUrl();
    }

    /**
     * Get separator line.
     *
     * @return string
     */
    protected function separatorLine()
    {
        return '/*' . str_repeat('=', 50) . '*/';
    }
}
