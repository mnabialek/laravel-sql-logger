<?php

namespace Mnabialek\LaravelSqlLogger\Providers;

use Mnabialek\LaravelSqlLogger\SqlLogger;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register()
    {
        // files to publish
        $this->publishes([
            realpath(__DIR__ .
                '/../../config/sql_logger.php') => config_path('sql_logger.php'),
        ]);

        // get settings
        $logStatus = $this->getSqlLoggingStatus();
        $slowLogStatus = $this->getSlowSqlLoggingStatus();
        $slowLogTime = $this->getSlowSqlLoggingTime();
        $override = $this->getOverrideStatus();
        $directory = $this->getLogDirectory();
        $convertToSeconds = $this->getConvertToSeconds();

        // if any of logging type is enabled we will listen database to get all
        // executed queries
        if ($logStatus || $slowLogStatus) {
            // create logger class
            $logger = $this->app->make(SqlLogger::class,
                [
                    $this->app,
                    $logStatus,
                    $slowLogStatus,
                    $slowLogTime,
                    $override,
                    $directory,
                    $convertToSeconds,
                ]);

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
     * Whether all queries should be logged
     *
     * @return bool
     */
    protected function getSqlLoggingStatus()
    {
        return (bool)$this->app->config->get('sql_logger.log_queries',
            env('SQL_LOG_QUERIES', false));
    }

    /**
     * Whether slow queries should be logged
     *
     * @return bool
     */
    protected function getSlowSqlLoggingStatus()
    {
        return (bool)$this->app->config->get('sql_logger.log_slow_queries',
            env('SQL_LOG_SLOW_QUERIES', false));
    }

    /**
     * Minimum execution time (in milliseconds) to consider query as slow
     *
     * @return float
     */
    protected function getSlowSqlLoggingTime()
    {
        return $this->app->config->get('sql_logger.slow_queries_min_exec_time',
            env('SQL_SLOW_QUERIES_MIN_EXEC_TIME', 100));
    }

    /**
     * Whether SQL log should be overridden for each request
     *
     * @return bool
     */
    protected function getOverrideStatus()
    {
        return (bool)$this->app->config->get('sql_logger.override_log',
            env('SQL_LOG_OVERRIDE', false));
    }

    /**
     * Get directory where log files should be saved
     *
     * @return string
     */
    protected function getLogDirectory()
    {
        return $this->app->config->get('sql_logger.directory',
            env('SQL_LOG_DIRECTORY', 'logs' . DIRECTORY_SEPARATOR . 'sql'));
    }

    /**
     * Whether query execution time should be converted to seconds
     *
     * @return bool
     */
    protected function getConvertToSeconds()
    {
        return (bool)$this->app->config->get('sql_logger.convert_to_seconds',
            env('SQL_CONVERT_TIME_TO_SECONDS', false));
    }
}
