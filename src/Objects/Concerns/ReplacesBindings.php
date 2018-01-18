<?php

namespace Mnabialek\LaravelSqlLogger\Objects\Concerns;

use DateTime;

trait ReplacesBindings
{
    /**
     * Replace bindings.
     *
     * @param string $sql
     * @param array $bindings
     *
     * @return string
     */
    protected function replaceBindings($sql, array $bindings)
    {
        $regex = $this->getRegex();

        foreach ($this->formatBindings($bindings) as $key => $binding) {
            $value = is_numeric($binding) ? $binding : "'" . $binding . "'";
            $sql = preg_replace($regex, $value, $sql, 1);
        }

        return $sql;
    }

    /**
     * Format bindings values.
     *
     * @param array $bindings
     *
     * @return array
     */
    protected function formatBindings($bindings)
    {
        foreach ($bindings as $key => $binding) {
            if ($binding instanceof DateTime) {
                $bindings[$key] = $binding->format('Y-m-d H:i:s');
            } elseif (is_string($binding)) {
                $bindings[$key] = str_replace("'", "\\'", $binding);
            }
        }

        return $bindings;
    }

    /**
     * Get regex to be used to replace bindings.
     * @return string
     */
    protected function getRegex()
    {
        return $this->wrapRegex($this->notInsideQuotes('?') . '|' . $this->notInsideQuotes('\:\w+', false));
    }

    /**
     * Wrap regex.
     *
     * @param string $regex
     *
     * @return string
     */
    protected function wrapRegex($regex)
    {
        return '#' . $regex . '#ms';
    }

    /**
     * Create partial regex to find given text not inside quotes.
     *
     * @param string $string
     * @param bool $quote
     *
     * @return string
     */
    protected function notInsideQuotes($string, $quote = true)
    {
        if ($quote) {
            $string = preg_quote($string);
        }

        return '(?:"[^"]*[^\\\]"(*SKIP)(*F)|' . $string . ')|(?:\'[^\']*[^\\\]\'(*SKIP)(*F)|' . $string . ')';
    }
}
