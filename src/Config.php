<?php

namespace Mnabialek\LaravelSqlLogger;

use Illuminate\Contracts\Config\Repository as ConfigRepository;

class Config
{
    /**
     * @var ConfigRepository
     */
    protected $repository;

    /**
     * Config constructor.
     *
     * @param ConfigRepository $repository
     */
    public function __construct(ConfigRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Whether all queries should be logged.
     *
     * @return bool
     */
    public function getSqlLoggingStatus()
    {
        return (bool) $this->repository->get('sql_logger.log_queries');
    }

    /**
     * Whether slow queries should be logged.
     *
     * @return bool
     */
    public function getSlowSqlLoggingStatus()
    {
        return (bool) $this->repository->get('sql_logger.log_slow_queries');
    }

    /**
     * Minimum execution time (in milliseconds) to consider query as slow.
     *
     * @return float
     */
    public function getSlowSqlLoggingTime()
    {
        return $this->repository->get('sql_logger.slow_queries_min_exec_time');
    }

    /**
     * Whether SQL log should be overridden for each request.
     *
     * @return bool
     */
    public function getOverrideStatus()
    {
        return (bool) $this->repository->get('sql_logger.override_log');
    }

    /**
     * Get directory where log files should be saved.
     *
     * @return string
     */
    public function getLogDirectory()
    {
        return $this->repository->get('sql_logger.directory');
    }

    /**
     * Whether query execution time should be converted to seconds.
     *
     * @return bool
     */
    public function getConvertToSeconds()
    {
        return (bool) $this->repository->get('sql_logger.convert_to_seconds');
    }

    /**
     * Whether console queries should be logged into separate files.
     *
     * @return bool
     */
    public function getSeparateConsoleLogs()
    {
        return (bool) $this->repository->get('sql_logger.log_console_to_separate_file');
    }
}
