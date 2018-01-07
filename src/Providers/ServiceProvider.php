<?php

namespace Mnabialek\LaravelSqlLogger\Providers;

use Mnabialek\LaravelSqlLogger\Config;
use Mnabialek\LaravelSqlLogger\SqlLogger;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * {@inheritdoc}
     */
    public function __construct($app)
    {
        parent::__construct($app);
        $this->config = $this->app->make(Config::class);
    }

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
        $logStatus = $this->config->logQueries();
        $slowLogStatus = $this->config->logSlowQueries();
        $slowLogTime = $this->config->slowLogTime();
        $override = $this->config->overrideFile();
        $directory = $this->config->logDirectory();
        $convertToSeconds = $this->config->useSeconds();
        $separateConsoleLog = $this->config->separateConsoleLogs();

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
}
