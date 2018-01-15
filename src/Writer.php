<?php

namespace Mnabialek\LaravelSqlLogger;

use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Mnabialek\LaravelSqlLogger\Objects\SqlQuery;

class Writer
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var Formatter
     */
    private $formatter;

    /**
     * @var Config
     */
    private $config;

    /**
     * Writer constructor.
     *
     * @param Application $app
     * @param Formatter $formatter
     * @param Config $config
     */
    public function __construct(Application $app, Formatter $formatter, Config $config)
    {
        $this->app = $app;
        $this->formatter = $formatter;
        $this->config = $config;
    }

    /**
     * Save queries to log.
     *
     * @param SqlQuery $query
     */
    public function save(SqlQuery $query)
    {
        $this->createDirectoryIfNotExists($query->number());

        $line = $this->formatter->getLine($query);

        if ($this->config->logAllQueries()) {
            $this->saveLine($line, $this->logName(), $this->shouldOverrideFile($query));
        }

        if ($this->shouldLogSlowQuery($query->time())) {
            $this->saveLine($line, $this->slowLogName());
        }
    }

    /**
     * Create directory if it does not exist yet.
     * 
     * @param int $queryNumber
     */
    protected function createDirectoryIfNotExists($queryNumber)
    {
        if ($queryNumber == 1 && ! file_exists($directory = $this->directory())) {
            mkdir($directory, 0777, true);
        }
    }

    /**
     * Get directory where file should be located.
     *
     * @return string
     */
    protected function directory()
    {
        return rtrim($this->config->logDirectory(), '\\/');
    }

    /**
     * Get log name.
     *
     * @return string
     */
    protected function logName()
    {
        return Carbon::now()->format('Y-m-d') . $this->suffix() . '-log.sql';
    }

    /**
     * Get slow log name.
     *
     * @return string
     */
    protected function slowLogName()
    {
        return Carbon::now()->format('Y-m-d') . $this->suffix() . '-slow-log.sql';
    }

    /**
     * Get file suffix.
     *
     * @return string
     */
    protected function suffix()
    {
        return $this->app->runningInConsole() ? $this->config->consoleSuffix() : '';
    }

    /**
     * Verify whether slow query should be logged.
     *
     * @param float $execTime
     *
     * @return bool
     */
    protected function shouldLogSlowQuery($execTime)
    {
        return $this->config->logSlowQueries() && $execTime >= $this->config->slowLogTime();
    }

    /**
     * Save data to log file.
     *
     * @param string $line
     * @param string $fileName
     * @param bool $override
     */
    protected function saveLine($line, $fileName, $override = false)
    {
        file_put_contents($this->directory() . DIRECTORY_SEPARATOR . $fileName,
            $line, $override ? 0 : FILE_APPEND);
    }

    /**
     * Verify whether file should be overridden.
     *
     * @param SqlQuery $query
     *
     * @return bool
     */
    private function shouldOverrideFile(SqlQuery $query)
    {
        return ($query->number() == 1 && $this->config->overrideFile());
    }
}
