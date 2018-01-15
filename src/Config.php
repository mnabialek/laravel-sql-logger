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
    public function logQueries()
    {
        return (bool) $this->repository->get('sql_logger.all_queries.enabled');
    }

    /**
     * Whether slow queries should be logged.
     *
     * @return bool
     */
    public function logSlowQueries()
    {
        return (bool) $this->repository->get('sql_logger.slow_queries.enabled');
    }

    /**
     * Minimum execution time (in milliseconds) to consider query as slow.
     *
     * @return float
     */
    public function slowLogTime()
    {
        return $this->repository->get('sql_logger.slow_queries.min_exec_time');
    }

    /**
     * Whether SQL log should be overridden for each request.
     *
     * @return bool
     */
    public function overrideFile()
    {
        return (bool) $this->repository->get('sql_logger.all_queries.override_log');
    }

    /**
     * Get directory where log files should be saved.
     *
     * @return string
     */
    public function logDirectory()
    {
        return $this->repository->get('sql_logger.general.directory');
    }

    /**
     * Whether query execution time should be converted to seconds.
     *
     * @return bool
     */
    public function useSeconds()
    {
        return (bool) $this->repository->get('sql_logger.general.use_seconds');
    }

    /**
     * Whether console queries should be logged into separate files.
     *
     * @return bool
     */
    public function separateConsoleLogs()
    {
        return (bool) $this->repository->get('sql_logger.general.separate_console_log');
    }
}
