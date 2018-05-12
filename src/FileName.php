<?php

namespace Mnabialek\LaravelSqlLogger;

use Carbon\Carbon;
use Illuminate\Container\Container;

class FileName
{
    /**
     * @var Container
     */
    private $app;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Carbon
     */
    private $now;

    /**
     * FileName constructor.
     *
     * @param Container $app
     * @param Config $config
     */
    public function __construct(Container $app, Config $config)
    {
        $this->app = $app;
        $this->config = $config;
        $this->now = Carbon::now();
    }

    /**
     * Get file name for all queries log.
     *
     * @return string
     */
    public function getForAllQueries()
    {
        return $this->createFileName($this->config->allQueriesFileName());
    }

    /**
     * Get file name for slow queries log.
     *
     * @return string
     */
    public function getForSlowQueries()
    {
        return $this->createFileName($this->config->slowQueriesFileName());
    }

    /**
     * Create file name.
     *
     * @param string $fileName
     *
     * @return string
     */
    protected function createFileName($fileName)
    {
        return $this->parseFileName($fileName) . $this->suffix() . $this->config->fileExtension();
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
     * Parse file name to include date in it.
     *
     * @param string $fileName
     *
     * @return string
     */
    protected function parseFileName($fileName)
    {
        return preg_replace_callback('#(\[.*\])#U', function ($matches) {
            $format = str_replace(['[',']'], [], $matches[1]);

            return $this->now->format($format);
        }, $fileName);
    }
}
