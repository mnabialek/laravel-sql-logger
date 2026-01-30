<?php

namespace Mnabialek\LaravelSqlLogger;

class CallerResolver
{
    /**
     * @var string
     */
    private $basePath;

    /**
     * @var string[]
     */
    private $excludePathFragments;

    /**
     * @param string $basePath Project base path (base_path())
     * @param string[] $excludePathFragments
     */
    public function __construct($basePath, array $excludePathFragments = [])
    {
        $this->basePath = rtrim((string) $basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        // Standard-Filter: vendor + dieses Package (damit wir "deinen" Code erwischen)
        $this->excludePathFragments = $excludePathFragments ?: [
            DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Providers' . DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'SqlLogger.php',
            DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Query.php',
            DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Writer.php',
            DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Formatter.php',
        ];
    }

    /**
     * @param array $trace debug_backtrace() result
     * @return string|null
     */
    public function resolve(array $trace)
    {
        foreach ($trace as $frame) {
            if (empty($frame['file']) || empty($frame['line'])) {
                continue;
            }

            $file = (string) $frame['file'];

            // Ausschließen: vendor & Logger-intern
            $skip = false;
            foreach ($this->excludePathFragments as $fragment) {
                if (strpos($file, $fragment) !== false) {
                    $skip = true;
                    break;
                }
            }
            if ($skip) {
                continue;
            }

            $rel = $this->toRelativePath($file);
            $line = (int) $frame['line'];

            $fn = '';
            if (!empty($frame['class']) && !empty($frame['function'])) {
                $fn = ' ' . $frame['class'] . '::' . $frame['function'];
            } elseif (!empty($frame['function'])) {
                $fn = ' ' . $frame['function'];
            }

            return $rel . ':' . $line . $fn;
        }

        return null;
    }

    private function toRelativePath($path)
    {
        $path = (string) $path;
        if (strpos($path, $this->basePath) === 0) {
            return substr($path, strlen($this->basePath));
        }
        return $path;
    }
}
