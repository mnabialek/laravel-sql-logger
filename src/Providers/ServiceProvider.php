<?php

namespace Mnabialek\LaravelSqlLogger\Providers;

use Mnabialek\LaravelSqlLogger\SqlLogger;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        // merge config
        $this->mergeConfigFrom(__DIR__ . '/../../publish/config/sql_logger.php', 'sql_logger');

        // register files to be published
        $this->publishes($this->getPublished());

        // get settings
        $logStatus = $this->getSqlLoggingStatus();
        $slowLogStatus = $this->getSlowSqlLoggingStatus();
        $slowLogTime = $this->getSlowSqlLoggingTime();
        $override = $this->getOverrideStatus();
        $directory = $this->getLogDirectory();
        $convertToSeconds = $this->getConvertToSeconds();
        $separateConsoleLog = $this->getSeparateConsoleLogs();

        // if any of logging type is enabled we will listen database to get all
        // executed queries
        if ($logStatus || $slowLogStatus) {
            // create logger class
            $logger = new SqlLogger($this->app, $logStatus, $slowLogStatus, $slowLogTime, $override,
                $directory, $convertToSeconds, $separateConsoleLog);

            // listen to database queries
            $this->app['db']->listen(function (
                $query,
                $bindings = null,
                $time = null
            ) use ($logger) {
                $logger->log($query, $bindings, $time);
            });
        }
    }

    /**
     * Get files to be published.
     *
     * @return array
     */
    protected function getPublished()
    {
        return [
            realpath(__DIR__ .
                '/../../publish/config/sql_logger.php') => (function_exists('config_path') ?
                    config_path('sql_logger.php') :
                    base_path('config/sql_logger.php')),
        ];
    }

    /**
     * Whether all queries should be logged.
     *
     * @return bool
     */
    protected function getSqlLoggingStatus()
    {
        return (bool) $this->app['config']->get('sql_logger.log_queries');
    }

    /**
     * Whether slow queries should be logged.
     *
     * @return bool
     */
    protected function getSlowSqlLoggingStatus()
    {
        return (bool) $this->app['config']->get('sql_logger.log_slow_queries');
    }

    /**
     * Minimum execution time (in milliseconds) to consider query as slow.
     *
     * @return float
     */
    protected function getSlowSqlLoggingTime()
    {
        return $this->app['config']->get('sql_logger.slow_queries_min_exec_time');
    }

    /**
     * Whether SQL log should be overridden for each request.
     *
     * @return bool
     */
    protected function getOverrideStatus()
    {
        return (bool) $this->app['config']->get('sql_logger.override_log');
    }

    /**
     * Get directory where log files should be saved.
     *
     * @return string
     */
    protected function getLogDirectory()
    {
        return $this->app['config']->get('sql_logger.directory');
    }

    /**
     * Whether query execution time should be converted to seconds.
     *
     * @return bool
     */
    protected function getConvertToSeconds()
    {
        return (bool) $this->app['config']->get('sql_logger.convert_to_seconds');
    }

    /**
     * Whether console queries should be logged into separate files.
     *
     * @return bool
     */
    protected function getSeparateConsoleLogs()
    {
        return (bool) $this->app['config']->get('sql_logger.log_console_to_separate_file');
    }
}
