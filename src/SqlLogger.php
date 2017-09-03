<?php

namespace Mnabialek\LaravelSqlLogger;

class SqlLogger
{
    /**
     * Application version.
     *
     * @var string
     */
    protected $version;

    /**
     * Whether SQL queries should be logged.
     *
     * @var bool
     */
    protected $logStatus;

    /**
     * Whether slow SQL queries should be logged.
     *
     * @var bool
     */
    protected $slowLogStatus;

    /**
     * Slow query execution time.
     *
     * @var float
     */
    protected $slowLogTime;

    /**
     * Whether log file should be overridden for each request.
     *
     * @var bool
     */
    protected $override;

    /**
     * Location where log files should be stored.
     *
     * @var string
     */
    protected $directory;

    /**
     * Whether query execution time should be converted to seconds.
     *
     * @var bool
     */
    protected $convertToSeconds;

    /**
     * Whether artisan queries should be saved into separate files.
     *
     * @var bool
     */
    protected $separateConsoleLog;

    /**
     * SqlLogger constructor.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param bool $logStatus
     * @param bool $slowLogStatus
     * @param float $slowLogTime
     * @param bool $override
     * @param string $directory
     * @param bool $convertToSeconds
     * @param bool $separateConsoleLog
     */
    public function __construct(
        $app,
        $logStatus,
        $slowLogStatus,
        $slowLogTime,
        $override,
        $directory,
        $convertToSeconds,
        $separateConsoleLog
    ) {
        $this->app = $app;
        $this->logStatus = $logStatus;
        $this->slowLogStatus = $slowLogStatus;
        $this->slowLogTime = $slowLogTime;
        $this->override = $override;
        $this->directory = rtrim($directory, '\\/');
        $this->convertToSeconds = $convertToSeconds;
        $this->separateConsoleLog = $separateConsoleLog;
    }

    /**
     * Log query.
     *
     * @param mixed $query
     * @param mixed $bindings
     * @param mixed $time
     */
    public function log($query, $bindings, $time)
    {
        static $queryNr = 0;

        ++$queryNr;

        try {
            list($sqlQuery, $execTime) = $this->getSqlQuery($query, $bindings, $time);
        } catch (\Exception $e) {
            $this->app->log->notice("SQL query {$queryNr} cannot be bound: " . $query);

            return;
        }

        $logData = $this->getLogData($queryNr, $sqlQuery, $execTime);

        $this->save($logData, $execTime, $queryNr);
    }

    /**
     * Save data to log file.
     *
     * @param string $data
     * @param int $execTime
     * @param int $queryNr
     */
    protected function save($data, $execTime, $queryNr)
    {
        $filePrefix = ($this->separateConsoleLog && $this->app->runningInConsole())
            ? '-artisan' : '';

        $this->createLogDirectoryIfNeeded($queryNr, $execTime);

        // save normal query to file if enabled
        if ($this->shouldLogQuery()) {
            $this->saveLog($data, date('Y-m-d') . $filePrefix . '-log.sql',
                ($queryNr == 1 && (bool) $this->override));
        }

        // save slow query to file if enabled
        if ($this->shouldLogSlowQuery($execTime)) {
            $this->saveLog($data, date('Y-m-d') . $filePrefix . '-slow-log.sql');
        }
    }

    /**
     * Verify whether query should be logged.
     *
     * @return bool
     */
    protected function shouldLogQuery()
    {
        return $this->logStatus;
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
        return $this->slowLogStatus && $execTime >= $this->slowLogTime;
    }

    /**
     * Save data to log file.
     *
     * @param string $data
     * @param string $fileName
     * @param bool $override
     */
    protected function saveLog($data, $fileName, $override = false)
    {
        file_put_contents($this->directory . DIRECTORY_SEPARATOR . $fileName,
            $data, $override ? 0 : FILE_APPEND);
    }

    /**
     * Get full query information to be used to save it.
     *
     * @param int $queryNr
     * @param string $query
     * @param float $execTime
     *
     * @return string
     */
    protected function getLogData($queryNr, $query, $execTime)
    {
        $time = $this->convertToSeconds ? ($execTime / 1000.0) . '.s'
            : $execTime . 'ms';

        return '/* Query ' . $queryNr . ' - ' . date('Y-m-d H:i:s') . ' [' .
            $time . ']' . "  */\n" . $query . ';' .
            "\n/*==================================================*/\n";
    }

    /**
     * Get SQL query and query exection time.
     *
     * @param mixed $query
     * @param mixed $bindings
     * @param mixed $execTime
     *
     * @return array
     */
    protected function getSqlQuery($query, $bindings, $execTime)
    {
        //for Laravel 5.2 $query is object and it holds the data
        if (version_compare($this->getVersion(), '5.2.0', '>=')) {
            $bindings = $query->bindings;
            $execTime = $query->time;
            $query = $query->sql;
        }

        // need to format bindings properly
        foreach ($bindings as $i => $binding) {
            if ($binding instanceof \DateTime) {
                $bindings[$i] = $binding->format('Y-m-d H:i:s');
            } elseif (is_string($binding)) {
                $bindings[$i] = str_replace("'", "\\'", $binding);
            }
        }

        // now we create full SQL query - in case of failure, we log this
        $query = str_replace(['%', '?', "\n"], ['%%', "'%s'", ' '], $query);
        $fullSql = vsprintf($query, $bindings);

        return [$fullSql, $execTime];
    }

    /**
     * Get framework version.
     *
     * @return string
     */
    protected function getVersion()
    {
        $version = $this->app->version();

        // for Lumen we need to do extra things to get Lumen version
        if (mb_strpos($version, 'Lumen') !== false) {
            $p = mb_strpos($version, '(');
            $p2 = mb_strpos($version, ')');
            if ($p !== false && $p2 !== false) {
                $version = trim(mb_substr($version, $p + 1, $p2 - $p - 1));
            }
        }

        return $version;
    }

    /**
     * Create log directory if it does not exist.
     *
     * @param int $queryNr
     * @param int $execTime
     */
    protected function createLogDirectoryIfNeeded($queryNr, $execTime)
    {
        if ($queryNr == 1 && ! file_exists($this->directory) &&
            ($this->shouldLogQuery() || $this->shouldLogSlowQuery($execTime))) {
            mkdir($this->directory, 0777, true);
        }
    }
}
